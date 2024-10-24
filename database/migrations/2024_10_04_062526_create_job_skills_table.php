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
        Schema::create('job_skills', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id'); // Foreign key to users table
            $table->unsignedBigInteger('skill_id'); // Foreign key to skills table

            // Foreign key constraints
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');

            // Composite primary key
            $table->primary(['job_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_skills');
    }
};
