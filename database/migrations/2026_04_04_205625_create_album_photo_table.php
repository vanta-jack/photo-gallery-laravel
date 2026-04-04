<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Album Photo Pivot Table
 * 
 * This is a pivot/junction table for many-to-many relationship between albums and photos.
 * One album can contain many photos, and one photo can belong to many albums.
 * 
 * This migration demonstrates:
 * - Pivot table naming convention (alphabetically ordered: album_photo not photo_album)
 * - Composite primary key (no separate id column needed)
 * - Using primary() to define multi-column primary keys
 * - Why pivot tables usually don't have timestamps (they just link records)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('album_photo', function (Blueprint $table) {
            // Foreign keys to both related tables
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            
            // Composite primary key: The combination of album_id and photo_id must be unique
            // This prevents adding the same photo to the same album multiple times
            // Laravel's convention: No 'id' column, the pair itself is the identifier
            $table->primary(['album_id', 'photo_id']);
            
            // Note: No timestamps() here because pivot tables typically just store relationships
            // If you need to track "when was this photo added to the album", you could add:
            // $table->timestamp('attached_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_photo');
    }
};
