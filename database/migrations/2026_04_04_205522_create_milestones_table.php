<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Milestones Table
 * 
 * Milestones track important life events with photos, particularly for tracking child development.
 * Examples: "Month 6", "First Day of Kindergarten", "High School Graduation"
 * 
 * This migration demonstrates:
 * - Enum columns for categorizing stages of life
 * - Flexible label system (stage provides broad category, label provides specific detail)
 * - Optional photo attachment (some milestones might not have photos yet)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // User who created this milestone (typically parent tracking child's growth)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Optional photo representing this milestone
            // Nullable because milestone might be created before photo is taken/uploaded
            $table->foreignId('photo_id')->nullable()->constrained()->cascadeOnDelete();
            
            // Stage: Broad category of life stage
            // Enum ensures only valid stages can be stored at database level
            $table->enum('stage', ['baby', 'grade_school', 'highschool_college']);
            
            // Label: Specific milestone within the stage
            // Examples: "Month 1", "Month 2" (baby), "Grade 3" (grade_school), "2nd Year HS" (highschool_college)
            // Using string for flexibility since labels vary widely across stages
            $table->string('label');
            
            // Description: Optional narrative about the milestone
            // "Johnny took his first steps!" or "Received honor roll certificate"
            $table->text('description')->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
