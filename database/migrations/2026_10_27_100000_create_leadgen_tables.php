<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);                // normalised, digits only incl. country code
            $table->string('email')->nullable();
            $table->string('student_name')->nullable();
            $table->string('class_interest')->nullable();
            $table->string('source')->default('manual');   // manual | excel | admission_form | whatsapp
            $table->enum('status', ['new', 'contacted', 'interested', 'follow_up', 'enrolled', 'not_interested'])
                  ->default('new')->index();
            $table->text('notes')->nullable();
            $table->boolean('consent')->default(true);     // permission to contact
            $table->boolean('opted_out')->default(false);  // replied STOP
            $table->boolean('ai_enabled')->default(true);  // false = human has taken over
            $table->boolean('needs_human')->default(false);
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->foreignId('message_id')->nullable();   // source row in `messages` (admission form)
            $table->timestamps();
            $table->unique('phone');
        });

        Schema::create('lead_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'call', 'note'])->default('whatsapp');
            $table->enum('direction', ['in', 'out'])->default('out');
            $table->longText('body')->nullable();          // message text or call transcript
            $table->string('status')->nullable();          // sent | failed | queued | completed ...
            $table->string('provider_id')->nullable()->unique(); // WhatsApp message id / call id (dedupe)
            $table->boolean('by_ai')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['lead_id', 'created_at']);
        });

        Schema::create('lead_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('channel', ['whatsapp', 'call'])->default('call');
            $table->text('content');                       // supports {name} {student_name} {class} {school}
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_scripts');
        Schema::dropIfExists('lead_messages');
        Schema::dropIfExists('leads');
    }
};
