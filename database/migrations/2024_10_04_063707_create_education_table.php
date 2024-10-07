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
        Schema::create('education', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('school_name'); // VARCHAR(128) for school name
            $table->string('degree')->nullable(); // Nullable VARCHAR(128) for degree
            $table->string('field_of_study')->nullable(); // Nullable VARCHAR(128) for field of study
            $table->string('start_date')->nullable(); // Nullable VARCHAR(128) for start date
            $table->string('end_date')->nullable(); // Nullable VARCHAR(128) for end date
            $table->string('description')->nullable(); // Nullable VARCHAR(128) for description
            $table->timestamps(); // Created at and updated at timestamps
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
