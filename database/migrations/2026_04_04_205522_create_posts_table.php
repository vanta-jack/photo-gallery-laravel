<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Posts Table
 * 
 * Posts are user-generated content entries, similar to blog posts or status updates.
 * This migration demonstrates:
 * - Text columns for longer content (description)
 * - Markdown support (noted in schema but stored as plain text - parsing happens in application)
 * - Simple structure focusing on content rather than media (photos can be attached via other tables)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Author of the post
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Title: Short summary or headline for the post
            $table->string('title');
            
            // Description: Main content of the post
            // Using text() for longer content that can include markdown syntax
            // Markdown parsing (converting **bold** to <strong>bold</strong>) happens in views/controllers
            // Database just stores the raw markdown text
            $table->text('description');
            
            // Timestamps: Important for displaying "Posted 2 hours ago" type messages
            $table->timestamps();
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
