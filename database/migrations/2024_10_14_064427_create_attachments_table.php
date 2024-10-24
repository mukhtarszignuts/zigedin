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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id')->nullable(); // FK for messages table
            $table->unsignedBigInteger('post_id')->nullable(); // FK for posts table
            $table->string('directory', 128);
            $table->string('file_name', 128);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
