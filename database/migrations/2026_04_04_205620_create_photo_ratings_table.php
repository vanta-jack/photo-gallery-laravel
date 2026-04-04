<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Photo Ratings Table
 * 
 * Allows users to rate photos on a 1-5 scale (like a 5-star rating system).
 * This migration demonstrates:
 * - Composite unique indexes (prevents duplicate ratings from same user on same photo)
 * - Tiny integer columns for small number ranges (1-5)
 * - Business logic hints in comments (average calculation mentioned)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('photo_ratings', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // The photo being rated
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            
            // The user who submitted this rating
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Rating value: 1 (worst) to 5 (best)
            // Using tinyInteger for values 0-255 (we only need 1-5)
            // Validation in the application ensures values stay within 1-5 range
            // Note: Average ratings are calculated in application code, not stored
            $table->tinyInteger('rating');
            
            // Timestamps
            $table->timestamps();
            
            // Composite unique index: Ensures each user can only rate a photo once
            // This prevents rating spam and ensures fair averages
            $table->unique(['photo_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_ratings');
    }
};
