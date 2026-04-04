<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Users Table
 * 
 * This migration creates the foundational users table for authentication and user management.
 * It demonstrates several Laravel migration concepts:
 * - Enum columns for type-safe role management at the database level
 * - Nullable columns with unique constraints (email can be null but must be unique when present)
 * - Proper timestamp handling with Laravel's timestamps() helper
 * - Foreign key setup (profile_photo_id will be constrained in a later migration)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * The up() method defines the table structure. Laravel will execute this when you run:
     * php artisan migrate
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key: auto-incrementing bigint column named 'id'
            $table->id();
            
            // Role enum: Database-level constraint ensuring only valid roles can be stored
            // Default 'guest' allows creating users before they fully register
            $table->enum("role", ["guest", "user", "admin"])->default("guest");
            
            // Email: Nullable but unique when present - supports guest users without email
            // The unique() constraint allows multiple NULL values but prevents duplicate non-null emails
            $table->string('email')->nullable()->unique();
            
            // Name fields: Separated for flexibility in display (e.g., "John Doe" vs "Doe, John")
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            
            // Password: Nullable to support OAuth/social login or guest users
            // Hashing is handled by Laravel's User model using the 'hashed' cast
            $table->string('password')->nullable();

            // Profile photo reference: Will be constrained after photos table is created
            // Using unsignedBigInteger for the foreign key to match photos.id type
            // Nullable because users don't need a profile photo
            $table->unsignedBigInteger("profile_photo_id")->nullable();
            
            // Timestamps: Automatically manages created_at and updated_at columns
            // Laravel's Eloquent ORM updates these automatically on insert/update
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     * 
     * The down() method undoes what up() did, allowing rollback with:
     * php artisan migrate:rollback
     * 
     * This is crucial for development and deployment flexibility - if something goes wrong,
     * you can easily undo database changes without manual SQL.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
