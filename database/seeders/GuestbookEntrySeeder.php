<?php

namespace Database\Seeders;

use App\Models\GuestbookEntry;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Database\Seeder;

class GuestbookEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get 10 random posts that don't already have guestbook entries
        $availablePosts = Post::whereDoesntHave('guestbookEntry')->take(10)->get();
        $photos = Photo::all();

        foreach ($availablePosts as $post) {
            GuestbookEntry::factory()->create([
                'post_id' => $post->id,
                'photo_id' => fake()->boolean(70) ? $photos->random()->id : null,
            ]);
        }
    }
}
