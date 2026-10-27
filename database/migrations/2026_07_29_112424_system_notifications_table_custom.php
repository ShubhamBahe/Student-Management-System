<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('message');
            $table->string('icon')->default('fas fa-bell');
            $table->string('color')->default('#2563eb');
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('type')->default('general');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('system_notifications');
    }
};