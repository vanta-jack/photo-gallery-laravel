<?php

namespace Database\Factories;

use App\Models\GuestbookEntry;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuestbookEntry>
 */
class GuestbookEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'photo_id' => fake()->boolean(60) ? Photo::factory() : null, // 60% have photos
        ];
    }
}
