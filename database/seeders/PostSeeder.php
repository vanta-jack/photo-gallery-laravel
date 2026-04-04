<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Create 30 posts with markdown content
        for ($i = 0; $i < 30; $i++) {
            Post::factory()->create([
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
