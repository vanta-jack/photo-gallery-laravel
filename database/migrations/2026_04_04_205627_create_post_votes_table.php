<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Post Votes Table
 * 
 * Simple upvote system for posts (like Reddit upvotes or Facebook likes).
 * Users can vote once per post to show appreciation or agreement.
 * 
 * This migration demonstrates:
 * - Engagement/interaction tracking tables
 * - Composite unique constraint (one vote per user per post)
 * - When timestamps are useful even in relationship tables (to show "trending" posts)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_votes', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // The post being voted on
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            
            // The user casting the vote
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Timestamps: created_at is particularly useful here
            // - Calculate "trending" posts based on recent votes
            // - Display "10 people voted in the last hour"
            // - Track voting patterns over time
            $table->timestamps();
            
            // Composite unique index: Each user can only vote once per post
            // This prevents vote manipulation and ensures fair engagement metrics
            $table->unique(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_votes');
    }
};
