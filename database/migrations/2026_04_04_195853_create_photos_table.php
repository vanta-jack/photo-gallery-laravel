<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Photos Table
 * 
 * The photos table is central to this gallery application. It stores photo metadata and file locations.
 * This migration demonstrates:
 * - Foreign key relationships with cascade delete (when user is deleted, their photos are deleted)
 * - Nullable text fields for optional descriptions
 * - String columns for file paths and titles
 * - Using foreignId() and constrained() for modern Laravel foreign key syntax
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the photos table with all necessary columns and relationships.
     */
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Foreign key to users table
            // foreignId() creates an UNSIGNED BIGINT column
            // constrained() automatically references users.id based on column name convention
            // cascadeOnDelete() ensures photos are deleted when the user is deleted
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Path: Storage location of the photo file (e.g., "photos/2024/01/sunset.jpg")
            // This should be relative to your storage disk configured in config/filesystems.php
            $table->string('path');
            
            // Title: Human-readable name for the photo
            $table->string('title');
            
            // Description: Optional detailed description of the photo
            // Using text() for longer content vs string() which is typically VARCHAR(255)
            $table->text('description')->nullable();
            
            // Timestamps: created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the photos table. Foreign key constraints are automatically dropped first.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
