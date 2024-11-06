<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT primary key
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->text('message');
            $table->integer('unseen_msgs')->nullable();
            $table->json('feedback')->nullable();
            $table->boolean('is_sent')->default(0);
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_seen')->default(0);
            $table->boolean('is_attachment')->default(false); // Flag to indicate if the message has an attachment
            $table->timestamps(); // Created at and updated at 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
