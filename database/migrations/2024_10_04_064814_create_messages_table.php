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
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT primary key
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->text('content'); // TEXT for message content
            $table->timestamp('sent_at'); // TIMESTAMP for when the message was sent
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
