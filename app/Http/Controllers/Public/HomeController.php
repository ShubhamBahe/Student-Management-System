<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Event;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\FeeType;
use App\Models\Message;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller {
    public function index() {
        $news         = News::where('status', 'published')->latest()->take(3)->get();
        $events       = Event::where('status', 'active')->orderBy('start_date')->take(4)->get();
        $teachers     = Teacher::with('user')->where('status', 'active')->take(4)->get();
        $testimonials = Testimonial::where('status', 'active')->take(4)->get();
        return view('public.home', compact('news', 'events', 'teachers', 'testimonials'));
    }

    public function about()    { return view('public.about'); }
    public function gallery()  { return view('public.gallery'); }
    public function faq()      { return view('public.faq'); }
    public function privacy()  { return view('public.privacy'); }
    public function terms()    { return view('public.terms'); }

    public function contact()  { return view('public.contact'); }

    public function sendContact(Request $request) {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);
        // 1. Save to MySQL database
        Message::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'unread',
        ]);
        // Notify admins about new message
        \App\Models\SystemNotification::notifyAdmins(
            'New Contact Message',
            'New message from ' . $request->name,
            route('dashboard.messages.index'),
            'fas fa-envelope',
            '#8b5cf6'
        );
        // 2. Send email notification
        try {
            Mail::to(setting('school_email', 'fayazahmedsaand93@gmail.com'))
                ->send(new ContactMail($request->all()));
        } catch (\Exception $e) {
            // Email failed but message saved to DB — still show success
        }
        return back()->with('success', 'Message sent successfully! We will reply soon. JazakuALLAH Khair');
    }

    public function admission() { return view('public.admission'); }

    public function applyAdmission(Request $request) {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'parent_name'  => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'email'        => 'required|email',
            'class'        => 'required|string',
        ]);
        // Save admission inquiry to messages table
        Message::create([
            'name'    => $request->parent_name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'subject' => 'Admission Application — ' . $request->class,
            'message' => 'Student Name: ' . $request->student_name .
                        "\nClass: " . $request->class .
                        "\nPrevious School: " . ($request->previous_school ?? 'N/A'),
            'status'  => 'unread',
        ]);
        // Lead Generation: turn the enquiry into a lead (never breaks the public form if it fails)
        try {
            $phone = \App\Services\LeadGen\Phone::normalize($request->phone);
            if ($phone) {
                $lead = \App\Models\Lead::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'name'           => $request->parent_name,
                        'email'          => $request->email,
                        'student_name'   => $request->student_name,
                        'class_interest' => $request->class,
                        'source'         => 'admission_form',
                        'consent'        => true,
                    ]
                );
                if ($lead->wasRecentlyCreated) {
                    \App\Models\SystemNotification::notifyAdmins(
                        'New admission lead',
                        $lead->name . ' — ' . ($lead->class_interest ?: 'admission enquiry'),
                        route('dashboard.leadgen.leads.show', $lead),
                        'fas fa-user-plus',
                        '#16a34a'
                    );
                    if (setting('leadgen_auto_welcome', '0') === '1') {
                        \App\Jobs\SendLeadWhatsApp::dispatch($lead->id);
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Lead capture failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Admission application submitted! We will contact you within 24 hours. JazakuALLAH Khair');
    }

    public function news() {
        $news = News::where('status', 'published')->latest()->paginate(9);
        return view('public.news', compact('news'));
    }

    public function newsDetail($slug) {
        $news = News::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('public.news-detail', compact('news'));
    }

    public function events() {
        $events = Event::where('status', 'active')->orderBy('start_date')->paginate(9);
        return view('public.events', compact('events'));
    }

    public function teachers() {
        $teachers = Teacher::with('user')->where('status', 'active')->paginate(12);
        return view('public.teachers', compact('teachers'));
    }

    public function feeStructure() {
        $feeTypes = FeeType::all();
        return view('public.fee-structure', compact('feeTypes'));
    }
}