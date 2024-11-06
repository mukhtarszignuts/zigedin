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
        Schema::table('employers', function (Blueprint $table) {
            $table->enum('type', ['company', 'school', 'showcase'])->nullable()->after('name');
            $table->string('organization_size')->nullable()->after('type');
            $table->enum('organization_type', [
                'pc',
                'se',
                'ga',
                'np',
                'sp',
                'ph',
                'ps'
            ])->nullable()->comment('Public Company,Self Employed','Government Agency','Nonprofit','Sole Proprietorship','Privately Held','Partnership')->after('organization_size');
            $table->string('public_url')->nullable()->after('organization_type');
            $table->string('tagline')->nullable()->after('public_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(['type','organization_size', 'organization_type', 'public_url','tagline']);
        });
    }
};
