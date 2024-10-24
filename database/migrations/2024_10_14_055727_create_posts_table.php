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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->text('content'); // Post content
            $table->string('tags')->nullable(); // Hashtags/tags associated with the post
            $table->string('mentions')->nullable(); // User mentions
            $table->boolean('is_active')->default(true);
            $table->boolean('is_attachment')->default(false); // Flag to indicate if the post has an attachment
            $table->enum('visibility', ['P','C'])->default('P')->comment('P = Public C = Connection only');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
