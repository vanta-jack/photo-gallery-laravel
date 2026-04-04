<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostVoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $posts = Post::all();

        // Create 60 votes on posts
        $createdCombinations = [];
        $attempts = 0;
        $maxAttempts = 120;

        while (count($createdCombinations) < 60 && $attempts < $maxAttempts) {
            $userId = $users->random()->id;
            $postId = $posts->random()->id;
            $key = "{$userId}-{$postId}";

            if (!isset($createdCombinations[$key])) {
                PostVote::factory()->create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                ]);
                $createdCombinations[$key] = true;
            }

            $attempts++;
        }
    }
}
