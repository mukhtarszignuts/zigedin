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
        Schema::create('connections', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->unsignedBigInteger('connection_id'); // Foreign key to skills table
            $table->enum('status', ['A', 'P', 'R'])->default('P'); // ENUM for connection status: Accepted, Pending, Rejected
            $table->datetime('request_date')->nullable();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('connection_id')->references('id')->on('users')->onDelete('cascade');

            // Composite primary key
            $table->primary(['user_id', 'connection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
