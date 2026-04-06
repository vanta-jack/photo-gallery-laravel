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
            $table->text('bio')->nullable()->after('profile_photo_id');
            $table->string('phone', 20)->nullable()->after('bio');
            $table->boolean('phone_public')->default(false)->after('phone');
            $table->string('linkedin')->nullable()->after('phone_public');
            $table->json('academic_history')->nullable()->after('linkedin');
            $table->json('professional_experience')->nullable()->after('academic_history');
            $table->json('skills')->nullable()->after('professional_experience');
            $table->json('certifications')->nullable()->after('skills');
            $table->string('orcid_id', 50)->nullable()->after('certifications');
            $table->string('github')->nullable()->after('orcid_id');
            $table->json('other_links')->nullable()->after('github');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'phone',
                'phone_public',
                'linkedin',
                'academic_history',
                'professional_experience',
                'skills',
                'certifications',
                'orcid_id',
                'github',
                'other_links',
            ]);
        });
    }
};
