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
            'baby' => ['Month ' . fake()->numberBetween(1, 24), 'First Steps', 'First Words'],
            'grade_school' => ['Grade ' . fake()->numberBetween(1, 6), 'First Day of School', 'Field Trip'],
            'highschool_college' => [fake()->numberBetween(1, 4) . 'st Year', 'Graduation', 'Prom Night'],
        ];

        return [
            'user_id' => User::factory(),
            'photo_id' => Photo::factory(),
            'stage' => $stage,
            'label' => fake()->randomElement($labels[$stage]),
            'description' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
