<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Photo Comments Table
 * 
 * Stores user comments on photos, enabling discussion and feedback on images.
 * This is a straightforward one-to-many relationship: one photo can have many comments.
 * This migration demonstrates:
 * - Simple foreign key relationships without unique constraints
 * - Text columns for user-generated content
 * - Cascade deletes to maintain data integrity
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photo_comments', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // The photo being commented on
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            
            // The user who wrote this comment
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Comment content: Using text() to allow longer comments
            // No length restriction at database level, but application may impose limits
            $table->text('body');
            
            // Timestamps: created_at helps display "2 hours ago" style relative times
            // updated_at tracks if user edits their comment (if that feature is implemented)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_comments');
    }
};
