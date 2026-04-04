<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Guestbook Entries Table
 * 
 * A guestbook is a special collection of posts, typically visitor messages with optional photos.
 * Think of it like a traditional website guestbook where visitors leave messages.
 * 
 * This migration demonstrates:
 * - One-to-one relationships (each guestbook entry links to exactly one post)
 * - Unique constraints on foreign keys
 * - Optional relationships (photo_id is nullable)
 * - Extending existing functionality (leveraging posts table for content)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guestbook_entries', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // One-to-one relationship with posts
            // Each guestbook entry is backed by a post (for title, description, etc.)
            // The unique constraint ensures each post can only be a guestbook entry once
            // This prevents duplicate entries and maintains data integrity
            $table->foreignId('post_id')->unique()->constrained()->cascadeOnDelete();
            
            // Optional photo attachment
            // Nullable because not all guestbook entries need a photo
            // Visitors might just leave a text message
            $table->foreignId('photo_id')->nullable()->constrained()->cascadeOnDelete();
            
            // Timestamps
            $table->timestamps();
            
            // Design note: By linking to posts table, guestbook entries automatically inherit:
            // - User association (post.user_id)
            // - Content (post.title and post.description)
            // - Voting capability (post_votes)
            // This is an example of composition over duplication
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guestbook_entries');
    }
};
