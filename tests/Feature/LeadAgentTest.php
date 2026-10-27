<?php
namespace Tests\Feature;

use App\Models\{AcademicYear, Attendance, Exam, Fee, FeeType, Mark, SchoolClass, Section, Student, Subject, User};
use App\Models\Lead\{LeadAuthAttempt, LeadGuardian, LeadOwnerAlert, LeadQueryLog};
use App\Services\LeadAgent\{AgentService, LanguageDetector};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeadAgentTest extends TestCase {
    use RefreshDatabase;

    private Student $aarav; private Student $diya; private $year, $class, $section, $user = 0;

    protected function setUp(): void {
        parent::setUp();
        config(['leadagent.ai.key' => null, 'leadagent.twilio.sid' => null, 'leadagent.owner_phone' => null]);
        $this->year = AcademicYear::create(['name' => '2026-27', 'start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'is_current' => true]);
        $this->class = SchoolClass::create(['academic_year_id' => $this->year->id, 'name' => 'Class 5']);
        $this->section = Section::create(['class_id' => $this->class->id, 'name' => 'A']);
        $this->aarav = $this->student('Aarav Sharma');
        LeadGuardian::create(['student_id' => $this->aarav->id, 'name' => 'Ramesh Sharma', 'phone' => '98765 43210', 'preferred_language' => 'en']);
    }

    private function student(string $name): Student {
        $u = User::create(['name' => $name, 'email' => 'u' . (++$this->user) . '@t.com', 'password' => 'secret123']);
        return Student::create(['user_id' => $u->id, 'roll_number' => '1' . $this->user . '00', 'admission_number' => 'ADM-2026-000' . $this->user,
            'admission_date' => '2026-04-01', 'class_id' => $this->class->id, 'section_id' => $this->section->id,
            'academic_year_id' => $this->year->id, 'status' => 'active']);
    }

    private function ask(string $text, string $phone = '+919876543210', array $extra = []): array {
        return app(AgentService::class)->handle(['phone' => $phone, 'text' => $text, 'channel' => 'web'] + $extra);
    }

    private function seedData(Student $s): void {
        $admin = User::create(['name' => 'Mrs Rao', 'email' => 'rao@t.com', 'password' => 'secret123']);
        // 10 recorded days, oldest first: 6 present, 1 late, 1 half, then 2 absent most recently
        $days = ['present', 'present', 'present', 'absent', 'present', 'late', 'present', 'half_day', 'absent', 'absent'];
        foreach ($days as $i => $st) {
            Attendance::create(['student_id' => $s->id, 'class_id' => $this->class->id, 'section_id' => $this->section->id,
                'date' => now()->subDays(10 - $i)->toDateString(), 'status' => $st, 'marked_by' => $admin->id,
                'remarks' => $i === 9 ? 'Called home, no response' : null]);
        }
        $maths = Subject::create(['name' => 'Maths', 'code' => 'MTH']);
        $sci = Subject::create(['name' => 'Science', 'code' => 'SCI']);
        $e1 = Exam::create(['name' => 'Unit Test 1', 'academic_year_id' => $this->year->id, 'start_date' => '2026-06-01', 'end_date' => '2026-06-05']);
        $e2 = Exam::create(['name' => 'Unit Test 2', 'academic_year_id' => $this->year->id, 'start_date' => '2026-08-01', 'end_date' => '2026-08-05']);
        foreach ([[$e1, $maths, 60, 'Needs practice'], [$e2, $maths, 80, 'Great improvement'], [$e1, $sci, 90, null], [$e2, $sci, 70, 'Careless mistakes']] as [$e, $sub, $m, $rem]) {
            Mark::create(['student_id' => $s->id, 'exam_id' => $e->id, 'subject_id' => $sub->id, 'marks_obtained' => $m, 'full_marks' => 100, 'remarks' => $rem]);
        }
        $ft = FeeType::create(['name' => 'Tuition', 'amount' => 5000]);
        Fee::create(['student_id' => $s->id, 'fee_type_id' => $ft->id, 'amount' => 5000, 'paid_amount' => 5000, 'status' => 'paid', 'due_date' => '2026-07-10']);
        Fee::create(['student_id' => $s->id, 'fee_type_id' => $ft->id, 'amount' => 5000, 'paid_amount' => 2000, 'status' => 'partial', 'due_date' => '2026-12-10']);
    }

    public function test_attendance_numbers_and_streak(): void {
        $this->seedData($this->aarav);
        $r = $this->ask('What is the attendance?');
        $this->assertSame('ok', $r['status']);
        // score = 3 present + 1 + 1 (late) + 1 + 0.5 (half day) = 6.5 of 10 => 65%
        $this->assertStringContainsString('overall 65%', $r['reply']);
        $this->assertStringContainsString('last 30 days 65%', $r['reply']);
        $this->assertStringContainsString('Current absence streak: 2 day(s)', $r['reply']);
        $this->assertStringContainsString('Recent absent dates:', $r['reply']);
    }

    public function test_marks_trend_and_strength(): void {
        $this->seedData($this->aarav);
        $en = $this->ask('Show my marks');
        $this->assertStringContainsString('Maths: average 70%', $en['reply']);
        $this->assertStringContainsString('+20 points', $en['reply']);
        $this->assertStringContainsString('improving', $en['reply']);
        $this->assertStringContainsString('Science: average 80%', $en['reply']);
        $this->assertStringContainsString('-20 points', $en['reply']);
        $this->assertStringContainsString('declining', $en['reply']);

        $s = $this->ask('weakest and strongest subject?');
        $this->assertStringContainsString('strongest subject is Science (average 80%)', $s['reply']);
        $this->assertStringContainsString('weakest subject is Maths (average 70%)', $s['reply']);
    }

    public function test_fees_and_remarks(): void {
        $this->seedData($this->aarav);
        $f = $this->ask('what is the fee status');
        $this->assertStringContainsString('total Rs 10,000, paid Rs 7,000, due Rs 3,000', $f['reply']);
        $this->assertStringContainsString('Due date: 10 Dec 2026', $f['reply']);

        $r = $this->ask('teacher remarks');
        $this->assertStringContainsString('Last 4 teacher remark(s)', $r['reply']);
        $this->assertStringContainsString('Called home, no response', $r['reply']);
        $this->assertStringContainsString('Great improvement', $r['reply']);
    }

    public function test_report_order_good_then_attention_then_one_suggestion(): void {
        $this->seedData($this->aarav);
        $r = $this->ask('full report please')['reply'];
        $g = strpos($r, 'Good news:'); $a = strpos($r, 'Needs attention:'); $s = strpos($r, 'Suggestion:');
        $this->assertTrue($g !== false && $a > $g && $s > $a, $r);
        $this->assertSame(1, substr_count($r, 'Suggestion:'));
        $this->assertStringContainsString('Maths is improving', $r);
        $this->assertStringContainsString('Science is declining', $r);
        $this->assertStringContainsString('Fee due: Rs 3,000', $r);
    }

    public function test_replies_match_parent_language(): void {
        $this->seedData($this->aarav);
        $this->assertSame('hinglish', LanguageDetector::detect('Fees kitni baaki hai?'));
        $this->assertSame('hi', LanguageDetector::detect('मेरे बच्चे की उपस्थिति बताइए'));
        $this->assertSame('en', LanguageDetector::detect('How is my child doing?'));

        $hing = $this->ask('Fees kitni baaki hai?');
        $this->assertStringContainsString('baaki Rs 3,000', $hing['reply']);
        $hi = $this->ask('मेरे बच्चे की उपस्थिति बताइए');
        $this->assertStringContainsString('उपस्थिति', $hi['reply']);
        $this->assertStringContainsString('लगातार अनुपस्थित: 2 दिन', $hi['reply']);
        $this->assertSame('hi', $hi['language']);
    }

    public function test_unknown_number_blocked_after_three_failed_attempts(): void {
        $p = '+919000011111';
        $r1 = $this->ask('hello', $p); $this->assertSame('unregistered', $r1['status']);
        $r2 = $this->ask('attendance', $p); $this->assertSame('unregistered', $r2['status']);
        $r3 = $this->ask('marks', $p);
        $this->assertSame('blocked', $r3['status']);
        $this->assertStringContainsString('60 minutes', $r3['reply']);
        $r4 = $this->ask('hello again', $p);
        $this->assertSame('blocked', $r4['status']);
        $this->assertDatabaseHas('lead_owner_alerts', ['type' => 'blocked_number']);

        // blocked number cannot get data even with a valid-looking ID
        $this->assertStringNotContainsString('Aarav', $this->ask('ADM-2026-0001', $p)['reply']);

        // block expires after 1 hour
        $this->travel(61)->minutes();
        $this->assertSame('unregistered', $this->ask('hi', $p)['status']);
    }

    public function test_unknown_student_id_alerts_owner(): void {
        $r = $this->ask('my student id is ADM-2026-9999', '+919111122222');
        $this->assertStringContainsString('could not find that student ID', $r['reply']);
        $a = LeadOwnerAlert::where('type', 'unknown_student_id')->first();
        $this->assertNotNull($a);
        $this->assertStringContainsString('ADM-2026-9999', $a->details);
    }

    public function test_complaint_refund_legal_go_to_owner_without_ai(): void {
        config(['leadagent.ai.key' => 'fake']);
        Http::fake();
        foreach (['I want a refund of my fees' => 'refund', 'Meri shikayat hai teacher ke baare mein' => 'complaint', 'I will contact my lawyer' => 'legal'] as $msg => $type) {
            $r = $this->ask($msg);
            $this->assertSame('escalated', $r['status']);
            $this->assertStringContainsString('school owner', $r['reply']);
            $this->assertDatabaseHas('lead_owner_alerts', ['type' => $type]);
        }
        Http::assertNothingSent();
        // even an unregistered sender is routed to the owner
        $this->assertSame('escalated', $this->ask('legal action lunga', '+918888877777')['status']);
    }

    public function test_two_children_asks_which_child_then_answers(): void {
        $this->seedData($this->aarav);
        $diya = $this->student('Diya Sharma');
        LeadGuardian::create(['student_id' => $diya->id, 'name' => 'Ramesh Sharma', 'phone' => '9876543210']);

        $q = $this->ask('attendance batao');
        $this->assertSame('clarify', $q['status']);
        $this->assertStringContainsString('1. Aarav Sharma', $q['reply']);
        $this->assertStringContainsString('2. Diya Sharma', $q['reply']);

        $a = $this->ask('1');
        $this->assertSame('ok', $a['status']);
        $this->assertStringContainsString('Aarav Sharma', $a['reply']);
        $this->assertStringContainsString('attendance', strtolower($a['reply']));
        $this->assertStringContainsString('overall 65%', $a['reply']);

        $b = $this->ask('Diya ki fees kitni hai');
        $this->assertStringContainsString('Diya Sharma', $b['reply']);
        $this->assertStringNotContainsString('Aarav', $b['reply']);
    }

    public function test_voice_note_or_image_asks_for_text(): void {
        foreach (['voice', 'image'] as $m) {
            $r = $this->ask('', '+919876543210', ['media' => $m]);   // guardian prefers English
            $this->assertSame('needs_text', $r['status']);
            $this->assertStringContainsString('type your question', $r['reply']);
        }
    }

    public function test_every_question_and_reply_is_logged(): void {
        $this->seedData($this->aarav);
        $this->ask('attendance'); $this->ask('I want a refund'); $this->ask('', '+919876543210', ['media' => 'voice']); $this->ask('hello', '+917000000001');
        $this->assertSame(4, LeadQueryLog::count());
        $this->assertTrue(LeadQueryLog::all()->every(fn ($l) => $l->reply !== null && $l->status !== null));
    }

    public function test_system_issue_fallback_when_ai_fails(): void {
        $this->seedData($this->aarav);
        config(['leadagent.ai.key' => 'fake']);
        Http::fake(['api.anthropic.com/*' => Http::response(['error' => 'boom'], 500)]);
        $r = $this->ask('attendance batao');
        $this->assertSame('error', $r['status']);
        $this->assertStringContainsString('system mein samasya', $r['reply']);
        $this->assertDatabaseHas('lead_query_logs', ['status' => 'error']);
        $this->assertDatabaseHas('lead_owner_alerts', ['type' => 'system_error']);
    }

    public function test_ai_mode_is_grounded_and_sends_facts(): void {
        $this->seedData($this->aarav);
        config(['leadagent.ai.key' => 'fake']);
        Http::fake(['api.anthropic.com/*' => Http::response(['content' => [['type' => 'text', 'text' => 'Aarav ki attendance 65% hai.']]], 200)]);
        $r = $this->ask('attendance kitni hai');
        $this->assertSame('Aarav ki attendance 65% hai.', $r['reply']);
        Http::assertSent(fn ($req) => str_contains($req['messages'][0]['content'], '"overall_percent":65')
            && str_contains($req['messages'][0]['content'], 'attendance kitni hai')
            && str_contains($req['system'], 'Hinglish'));
    }

    public function test_system_issue_when_database_fails(): void {
        \Illuminate\Support\Facades\Schema::drop('attendances');
        $r = $this->ask('attendance batao');
        $this->assertSame('error', $r['status']);
        $this->assertStringContainsString('system mein samasya', $r['reply']);
    }

    public function test_admin_pages_render_and_are_protected(): void {
        $this->get('/dashboard/lead-generation')->assertRedirect('/login');

        Role::firstOrCreate(['name' => 'super_admin']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@t.com', 'password' => 'secret123']);
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        foreach (['', '/console', '/guardians', '/scripts', '/calls', '/logs', '/logs?tab=alerts', '/logs?tab=blocked', '/settings'] as $path) {
            $this->get('/dashboard/lead-generation' . $path)->assertOk();
        }
        $this->post('/dashboard/lead-generation/scripts/samples')->assertRedirect();
        $this->assertDatabaseCount('lead_call_scripts', 4);

        $this->postJson('/dashboard/lead-generation/console/send', ['phone' => '9876543210', 'message' => 'hello'])
            ->assertOk()->assertJsonPath('status', 'ok');
        $this->get('/dashboard/lead-generation/logs/export')->assertOk();
    }

    public function test_webhooks_require_twilio_signature(): void {
        $this->post('/webhooks/lead/whatsapp', ['From' => 'whatsapp:+919876543210', 'Body' => 'hi'])->assertStatus(503);

        config(['leadagent.twilio.sid' => 'AC123', 'leadagent.twilio.token' => 'tok']);
        $this->post('/webhooks/lead/whatsapp', ['From' => 'whatsapp:+919876543210', 'Body' => 'hi'])->assertStatus(403);

        $params = ['From' => 'whatsapp:+919876543210', 'Body' => 'attendance', 'NumMedia' => '0'];
        $url = 'http://localhost/webhooks/lead/whatsapp';
        ksort($params); $data = $url; foreach ($params as $k => $v) $data .= $k . $v;
        $sig = base64_encode(hash_hmac('sha1', $data, 'tok', true));
        $res = $this->post('/webhooks/lead/whatsapp', $params, ['X-Twilio-Signature' => $sig]);
        $res->assertOk();
        $this->assertStringContainsString('<Message>', $res->getContent());
    }

    public function test_outbound_scripted_call_flow(): void {
        $this->seedData($this->aarav);
        config(['leadagent.twilio.sid' => 'AC123', 'leadagent.twilio.token' => 'tok', 'leadagent.twilio.voice_from' => '+15005550006',
                'app.url' => 'https://school.example.com', 'leadagent.call_window' => ['start' => '00:00', 'end' => '23:59']]);
        Http::fake(['api.twilio.com/*' => Http::response(['sid' => 'CA999'], 201)]);

        Role::firstOrCreate(['name' => 'super_admin']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@t.com', 'password' => 'secret123']);
        $admin->assignRole('super_admin');
        $this->actingAs($admin)->post('/dashboard/lead-generation/scripts/samples');
        $script = \App\Models\Lead\LeadCallScript::where('purpose', 'fee_reminder')->where('language', 'hinglish')->first();

        $this->post('/dashboard/lead-generation/calls', ['script_id' => $script->id, 'audience' => 'fee_due'])->assertRedirect();
        $call = \App\Models\Lead\LeadCall::first();
        $this->assertSame('calling', $call->status);
        $this->assertSame('CA999', $call->provider_sid);
        $this->assertStringContainsString('Rs 3,000', $call->script_text);
        $this->assertStringContainsString('Ramesh Sharma', $call->script_text);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'Calls.json') && str_contains($r['Url'], 'signature=') && $r['To'] === '+919876543210');

        // Twilio fetches the script (needs a valid Twilio signature + Laravel signed URL)
        $signed = \Illuminate\Support\Facades\URL::signedRoute('lead.webhook.outbound', ['call' => $call->id]);
        $sign = function (string $url, array $params) {
            ksort($params); $d = $url; foreach ($params as $k => $v) $d .= $k . $v;
            return ['X-Twilio-Signature' => base64_encode(hash_hmac('sha1', $d, 'tok', true))];
        };
        $res = $this->post($signed, [], $sign($signed, []));
        $res->assertOk();
        $this->assertStringContainsString('Gather', $res->getContent());
        $this->assertStringContainsString('Rs 3,000', $res->getContent());

        // parent asks a follow-up question during the call -> answered about the same child, logged in transcript
        $respond = \Illuminate\Support\Facades\URL::signedRoute('lead.webhook.outbound-respond', ['call' => $call->id, 'n' => 1]);
        $p = ['SpeechResult' => 'attendance kitni hai'];
        $r2 = $this->post($respond, $p, $sign($respond, $p));
        $r2->assertOk();
        $this->assertStringContainsString('overall 65%', str_replace('overall 65%', 'overall 65%', $call->fresh()->transcript) ?: '');
        $this->assertStringContainsString('PARENT: attendance kitni hai', $call->fresh()->transcript);

        // an unsigned URL is rejected
        $this->post('/webhooks/lead/voice/outbound/' . $call->id, [], $sign('http://localhost/webhooks/lead/voice/outbound/' . $call->id, []))->assertStatus(403);

        // status callback
        $status = route('lead.webhook.voice-status', $call);
        $sp = ['CallStatus' => 'completed', 'CallDuration' => '42'];
        $this->post($status, $sp, $sign($status, $sp))->assertStatus(204);
        $this->assertSame('completed', $call->fresh()->status);
        $this->assertSame(42, $call->fresh()->duration);
    }

    public function test_inbound_call_uses_agent(): void {
        $this->seedData($this->aarav);
        config(['leadagent.twilio.sid' => 'AC123', 'leadagent.twilio.token' => 'tok']);
        $sign = function (string $url, array $params) {
            ksort($params); $d = $url; foreach ($params as $k => $v) $d .= $k . $v;
            return ['X-Twilio-Signature' => base64_encode(hash_hmac('sha1', $d, 'tok', true))];
        };
        $url = 'http://localhost/webhooks/lead/voice/respond';
        $p = ['From' => '+919876543210', 'SpeechResult' => 'what are the fees due'];
        $res = $this->post($url, $p, $sign($url, $p));
        $res->assertOk();
        $this->assertStringContainsString('due Rs 3,000', $res->getContent());
        $this->assertStringContainsString('en-IN', $res->getContent());
    }
}
