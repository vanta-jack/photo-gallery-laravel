<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Milestone Model
 * 
 * Tracks important life events with optional photos, categorized by life stage.
 * Common use case: tracking child development (baby months, school years, etc.)
 */
#[Fillable(['user_id', 'photo_id', 'stage', 'label', 'description', 'is_public'])]
class Milestone extends Model
{
    /** @use HasFactory<\Database\Factories\MilestoneFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    public const CURATED_STAGE_OPTIONS = [
        'baby' => 'Baby',
        'toddler' => 'Toddler',
        'preschool' => 'Preschool',
        'grade_school' => 'Grade School',
        'middle_school' => 'Middle School',
        'high_school' => 'High School',
        'college' => 'College',
        'adult' => 'Adult',
        'highschool_college' => 'High School / College (Legacy)',
    ];

    /**
     * @return array<string, string>
     */
    public static function curatedStageOptions(): array
    {
        return self::CURATED_STAGE_OPTIONS;
    }

    /**
     * @return array<int, string>
     */
    public static function curatedStageValues(): array
    {
        return array_keys(self::CURATED_STAGE_OPTIONS);
    }

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    // Relationships

    /**
     * Get the user who created this milestone.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photo representing this milestone.
     * 
     * Optional - milestone might be created before photo is available.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
