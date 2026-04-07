<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stage = fake()->randomElement([
            'baby',
            'toddler',
            'preschool',
            'grade_school',
            'middle_school',
            'high_school',
            'college',
            'adult',
        ]);

        $labels = [
            'baby' => [
                'Baby · First Smile',
                'Baby · First Crawl',
            ],
            'toddler' => [
                'Toddler · First Steps',
                'Toddler · First Full Sentence',
            ],
            'preschool' => [
                'Preschool · First Day of Preschool',
                'Preschool · Favorite Story Time',
            ],
            'grade_school' => [
                'Grade School · Grade 1 Kickoff',
                'Grade School · First School Play',
            ],
            'middle_school' => [
                'Middle School · Science Fair Finalist',
            ],
            'high_school' => [
                'High School · Freshman Orientation',
                'High School · Varsity Team Tryout',
            ],
            'college' => [
                'College · Capstone Presentation',
                'College · Internship Offer',
            ],
            'adult' => [
                'Adult · First Career Role',
                'Adult · Leadership Promotion',
            ],
        ];

        return [
            'user_id' => User::factory(),
            'photo_id' => null,
            'stage' => $stage,
            'label' => fake()->randomElement($labels[$stage]),
            'description' => fake()->optional(0.7)->paragraph(),
            'is_public' => fake()->boolean(40),
        ];
    }

    /**
     * Associate the milestone with a photo.
     */
    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_id' => Photo::factory(),
        ]);
    }
}
