<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Parent / guardian phone numbers (this is the "Students sheet" the agent checks)
        Schema::create('lead_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('relation')->default('Parent');   // Father / Mother / Guardian
            $table->string('phone', 20)->index();             // normalised: last 10 digits
            $table->string('preferred_language', 10)->default('hinglish'); // hi | hinglish | en
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['student_id', 'phone']);
        });

        // Query_Log: every question + reply
        Schema::create('lead_query_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->nullable()->index();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 20)->default('web');     // web | whatsapp | voice | outbound
            $table->string('language', 10)->nullable();
            $table->string('intent', 120)->nullable();
            $table->string('status', 20)->index();             // ok | blocked | escalated | needs_text | unregistered | clarify | error
            $table->text('question')->nullable();
            $table->text('reply')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });

        // Failed attempts / temporary blocks
        Schema::create('lead_auth_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->unique();
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamp('last_failed_at')->nullable();
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();
        });

        // Alerts for the owner
        Schema::create('lead_owner_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();  // unknown_student_id | complaint | refund | legal | blocked_number | system_error
            $table->string('title');
            $table->text('details')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('sender_name')->nullable();
            $table->boolean('forwarded')->default(false);   // sent to owner's phone
            $table->string('status', 15)->default('new');   // new | handled
            $table->timestamps();
        });

        // Conversation state (which child did the parent mean?)
        Schema::create('lead_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 30)->unique();
            $table->json('pending')->nullable();
            $table->string('language', 10)->nullable();
            $table->timestamps();
        });

        // Scripts for outbound calls
        Schema::create('lead_call_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('purpose')->default('general');  // fee_reminder | attendance | ptm | general
            $table->string('language', 10)->default('hinglish');
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Outbound calls
        Schema::create('lead_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('script_id')->nullable()->constrained('lead_call_scripts')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guardian_id')->nullable()->constrained('lead_guardians')->nullOnDelete();
            $table->string('phone', 30);
            $table->string('status', 20)->default('queued'); // queued | calling | completed | failed | no_answer | skipped
            $table->string('provider_sid')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->longText('script_text')->nullable();     // rendered script that was read
            $table->longText('transcript')->nullable();
            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        foreach (['lead_calls','lead_call_scripts','lead_sessions','lead_owner_alerts','lead_auth_attempts','lead_query_logs','lead_guardians'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
