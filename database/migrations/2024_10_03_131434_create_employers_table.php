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
        Schema::create('employers', function (Blueprint $table) {
            $table->id(); // Automatically creates a BIGINT primary key 'id'
            $table->unsignedBigInteger('user_id'); // Foreign key reference
            $table->string('name')->unique(); // Unique name
            $table->string('industry')->nullable(); // Nullable industry
            $table->string('logo')->nullable(); // Nullable logo
            $table->string('location')->nullable(); // Nullable location
            $table->string('website')->nullable(); // Nullable website
            $table->timestamps(); 
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
