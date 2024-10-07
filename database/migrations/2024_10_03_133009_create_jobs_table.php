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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('emp_id')->constrained('employers')->onDelete('cascade'); // Foreign key to employer table
            $table->enum('job_type', ['F', 'P', 'FL']); 
            $table->enum('work_mode', ['WFH', 'WFO', 'HYB']); 
            $table->string('title'); 
            $table->string('description')->unique(); 
            $table->string('location')->nullable(); 
            $table->string('salary_range')->nullable(); 
            $table->timestamp('posted_at')->nullable(); 
            $table->enum('status', ['O', 'C']); 
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
