<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Already created in create_books_table migration
        // This file is intentionally empty
    }

    public function down(): void
    {
        // Nothing to drop here
    }
};