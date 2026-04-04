<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'path' => 'photos/' . fake()->dateTimeBetween('-2 years')->format('Y/m') . '/' . fake()->uuid() . '.jpg',
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
