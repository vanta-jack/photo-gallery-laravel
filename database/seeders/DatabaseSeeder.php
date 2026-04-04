<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * 
     * Seeds are called in dependency order to ensure relationships are valid:
     * 1. Users (no dependencies)
     * 2. Photos (depends on users)
     * 3. Albums (depends on users)
     * 4. Posts (depends on users)
     * 5. Milestones (depends on users and photos)
     * 6. AlbumPhoto pivot (depends on albums and photos)
     * 7. PhotoRatings (depends on users and photos)
     * 8. PhotoComments (depends on users and photos)
     * 9. PostVotes (depends on users and posts)
     * 10. GuestbookEntries (depends on posts and photos)
     */
    public function run(): void
    {
        // Step 1: Create users (foundation layer - no dependencies)
        $this->call(UserSeeder::class);

        // Step 2: Create photos (depends on users)
        $this->call(PhotoSeeder::class);

        // Step 3: Create albums (depends on users)
        $this->call(AlbumSeeder::class);

        // Step 4: Create posts (depends on users)
        $this->call(PostSeeder::class);

        // Step 5: Create milestones (depends on users and photos)
        $this->call(MilestoneSeeder::class);

        // Step 6: Attach photos to albums (depends on albums and photos)
        $this->call(AlbumPhotoSeeder::class);

        // Step 7: Create photo ratings (depends on users and photos)
        $this->call(PhotoRatingSeeder::class);

        // Step 8: Create photo comments (depends on users and photos)
        $this->call(PhotoCommentSeeder::class);

        // Step 9: Create post votes (depends on users and posts)
        $this->call(PostVoteSeeder::class);

        // Step 10: Create guestbook entries (depends on posts and photos)
        $this->call(GuestbookEntrySeeder::class);
    }
}
