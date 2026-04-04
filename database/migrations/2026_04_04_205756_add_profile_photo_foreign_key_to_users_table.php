<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Profile Photo Foreign Key to Users Table
 * 
 * This migration resolves the circular dependency between users and photos tables.
 * The problem: Users have profile_photo_id, and Photos have user_id
 * The solution: Create both tables first, then add the foreign key constraint afterward
 * 
 * This migration demonstrates:
 * - Handling circular foreign key dependencies
 * - Altering existing tables with Schema::table() instead of Schema::create()
 * - Adding constraints to existing columns
 * - Why migration order matters for referential integrity
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds the foreign key constraint to the existing profile_photo_id column.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add foreign key constraint to the existing profile_photo_id column
            // The column already exists from the initial users migration
            // Now we're just adding the constraint that references photos.id
            // 
            // Using nullOnDelete() because if the profile photo is deleted,
            // we want to set profile_photo_id to null rather than delete the user
            // (cascade delete would delete the user when their profile photo is deleted!)
            $table->foreign('profile_photo_id')
                  ->references('id')
                  ->on('photos')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Removes the foreign key constraint, allowing clean rollback.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            // Laravel automatically names it: users_profile_photo_id_foreign
            $table->dropForeign(['profile_photo_id']);
        });
    }
};
