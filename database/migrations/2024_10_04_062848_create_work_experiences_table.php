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
            $table->string('title');
            $table->enum('employment_type', ['F', 'P', 'FL','SE','I','T'])->comment('F=Full-time , P=Part-time FL=freelance , SE=self-employed , I = Intership , T=trainee');
            $table->string('company_name'); // Unique for company name
            $table->date('start_date'); // Date for job start date
            $table->date('end_date')->nullable(); // Nullable date for job end date
            $table->text('description')->nullable(); // Nullable TEXT for job responsibilities or achievements
            $table->string('location')->nullable();
            $table->enum('location_type', ['OS', 'RMT', 'HYB']); //On-site,Hybrid,Remote
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
