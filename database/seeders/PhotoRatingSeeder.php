<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\PhotoRating;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhotoRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $photos = Photo::all();

        // Create 100 ratings ensuring no duplicates per user/photo
        $createdCombinations = [];
        $attempts = 0;
        $maxAttempts = 200;

        while (count($createdCombinations) < 100 && $attempts < $maxAttempts) {
            $userId = $users->random()->id;
            $photoId = $photos->random()->id;
            $key = "{$userId}-{$photoId}";

            if (!isset($createdCombinations[$key])) {
                PhotoRating::factory()->create([
                    'user_id' => $userId,
                    'photo_id' => $photoId,
                    'rating' => fake()->numberBetween(1, 5),
                ]);
                $createdCombinations[$key] = true;
            }

            $attempts++;
        }
    }
}
