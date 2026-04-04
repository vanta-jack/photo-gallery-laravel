<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Albums Table
 * 
 * Albums group related photos together, like "Summer Vacation 2024" or "Family Portraits".
 * This migration demonstrates:
 * - Self-referencing foreign keys (cover_photo_id points to photos)
 * - Default values for columns (empty string for title, false for is_private)
 * - Boolean columns for privacy settings
 * - Circular foreign key handling (cover_photo_id is nullable and will be set after photos exist)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Owner of the album
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Cover photo: Optional featured photo for the album thumbnail
            // Nullable because album might be empty initially, or user hasn't chosen a cover yet
            // Using unsignedBigInteger instead of foreignId to add constraint later (after photos exist)
            $table->unsignedBigInteger('cover_photo_id')->nullable();
            
            // Title: Name of the album, defaults to empty string to avoid null checks
            // In real apps, you'd likely validate this isn't empty at the application level
            $table->string('title')->default('');
            
            // Description: Optional details about the album
            $table->text('description')->nullable();
            
            // Privacy flag: Controls whether album is visible to other users
            // false (public) by default, following "open by default" principle
            $table->boolean('is_private')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Note: Foreign key for cover_photo_id will be added in a later migration
            // after the album_photo pivot table exists, to avoid constraint issues
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
