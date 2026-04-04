<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhotoCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $photos = Photo::all();

        // Create 80 comments on photos
        for ($i = 0; $i < 80; $i++) {
            PhotoComment::factory()->create([
                'user_id' => $users->random()->id,
                'photo_id' => $photos->random()->id,
            ]);
        }
    }
}
