<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Create 15 albums distributed across users
        foreach ($users as $user) {
            // Each user gets 1-2 albums
            $albumCount = fake()->numberBetween(1, 2);
            Album::factory()->count($albumCount)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
