<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhotoComment>
 */
class PhotoCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Beautiful shot!',
            'Love the composition!',
            'This is amazing!',
            'Great lighting!',
            'Stunning colors!',
        ];

        return [
            'photo_id' => Photo::factory(),
            'user_id' => User::factory(),
            'body' => fake()->randomElement([
                fake()->randomElement($comments),
                fake()->sentence(),
                fake()->paragraph(),
            ]),
        ];
    }
}
