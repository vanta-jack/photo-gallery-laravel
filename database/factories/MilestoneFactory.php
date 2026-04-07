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
        $stage = fake()->randomElement(['baby', 'grade_school', 'highschool_college']);

        $labels = [
            'baby' => [
                'Baby · First Smile',
                'Toddler · First Steps',
            ],
            'grade_school' => [
                'Preschool · First Day of Preschool',
                'Grade School · Grade 1 Kickoff',
                'Middle School · Science Fair Finalist',
            ],
            'highschool_college' => [
                'High School · Freshman Orientation',
                'College · Capstone Presentation',
                'Adult · First Career Role',
            ],
        ];

        return [
            'user_id' => User::factory(),
            'photo_id' => null,
            'stage' => $stage,
            'label' => fake()->randomElement($labels[$stage]),
            'description' => fake()->optional(0.7)->paragraph(),
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
