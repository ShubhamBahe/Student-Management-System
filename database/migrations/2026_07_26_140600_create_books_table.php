<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Create book_categories FIRST inside this file
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Then create books
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('book_categories')->onDelete('cascade');
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique()->nullable();
            $table->string('publisher')->nullable();
            $table->string('publish_year')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('rack_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
    }
};