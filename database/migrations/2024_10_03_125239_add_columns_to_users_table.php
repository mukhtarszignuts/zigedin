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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->string('city')->nullable()->after('phone');
            $table->string('headline')->nullable()->after('city');
            $table->string('summary')->nullable()->after('headline');
            $table->enum('role',['A','C','E'])->default('C')->comment('A : Admin , C : Candidate , E : Employer')->after('summary');
            $table->boolean('is_active')->default(true);
            $table->string('profile_image')->nullable()->after('role');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('city');
            $table->dropColumn('headline');
            $table->dropColumn('summary');
            $table->dropColumn('role');
            $table->dropColumn('is_active');
            $table->dropColumn('profile_image');
            $table->dropColumn('deleted_at');
        });
    }
};
