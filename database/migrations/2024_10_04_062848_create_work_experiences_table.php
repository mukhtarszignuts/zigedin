<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('job_title'); 
            $table->string('company_name')->unique(); // Unique for company name
            $table->date('start_date'); // Date for job start date
            $table->date('end_date')->nullable(); // Nullable date for job end date
            $table->text('description')->nullable(); // Nullable TEXT for job responsibilities or achievements
            $table->timestamps(); // Created at and updated at timestamps
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
