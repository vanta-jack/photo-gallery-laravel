<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class AlbumPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $albums = Album::all();

        foreach ($albums as $album) {
            // Get photos from the same user who owns the album
            $userPhotos = Photo::where('user_id', $album->user_id)->get();

            if ($userPhotos->isEmpty()) {
                continue;
            }

            // Attach 3-10 photos per album (or fewer if user has fewer photos)
            $photoCount = min(fake()->numberBetween(3, 10), $userPhotos->count());
            $selectedPhotos = $userPhotos->random($photoCount);

            $album->photos()->attach($selectedPhotos->pluck('id'));

            // Set the first photo as cover photo
            $album->update([
                'cover_photo_id' => $selectedPhotos->first()->id,
            ]);
        }
    }
}
