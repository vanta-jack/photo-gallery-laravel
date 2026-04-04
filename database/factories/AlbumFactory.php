<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
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
            'cover_photo_id' => null, // Will be set after photos are attached
            'title' => fake()->words(3, true),
            'description' => fake()->optional(0.6)->paragraph(),
            'is_private' => fake()->boolean(30), // 30% chance of private
        ];
    }
}
