<?php

namespace Tests\Feature;

use App\Models\GuestbookEntry;
use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestbookFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_guestbook_feed_renders_content_cues_and_engagement_counts(): void
    {
        $author = User::factory()->user()->create([
            'first_name' => 'Lina',
            'last_name' => 'Hart',
            'profile_photo_id' => null,
        ]);

        $post = Post::factory()->for($author)->create([
            'title' => 'Summer Journal',
            'description' => 'First day in the city.',
        ]);

        $photo = Photo::factory()->for($author)->create([
            'title' => 'City lights',
        ]);

        GuestbookEntry::factory()->create([
            'post_id' => $post->id,
            'photo_id' => $photo->id,
        ]);

        PostVote::factory()->for($post)->for(User::factory()->user(), 'user')->create();
        PostVote::factory()->for($post)->for(User::factory()->user(), 'user')->create();
        PhotoRating::factory()->for($photo)->for(User::factory()->user(), 'user')->create();
        PhotoComment::factory()->for($photo)->for(User::factory()->user(), 'user')->create();
        PhotoComment::factory()->for($photo)->for(User::factory()->user(), 'user')->create();

        $this->get(route('guestbook.index'))
            ->assertOk()
            ->assertSeeText('Guestbook Feed')
            ->assertSeeText('Photo entry')
            ->assertSeeText('Summer Journal')
            ->assertSeeText('2 votes')
            ->assertSeeText('1 ratings')
            ->assertSeeText('2 comments')
            ->assertSeeText('LH');
    }

    public function test_guestbook_feed_lists_newest_entries_first(): void
    {
        $author = User::factory()->user()->create();

        $olderEntry = GuestbookEntry::factory()->create([
            'post_id' => Post::factory()->for($author)->create([
                'title' => 'Older entry',
            ])->id,
        ]);

        $newerEntry = GuestbookEntry::factory()->create([
            'post_id' => Post::factory()->for($author)->create([
                'title' => 'Newer entry',
            ])->id,
        ]);

        $olderEntry->created_at = now()->subHours(4);
        $olderEntry->save();

        $newerEntry->created_at = now()->subHour();
        $newerEntry->save();

        $this->get(route('guestbook.index'))
            ->assertOk()
            ->assertSeeTextInOrder(['Newer entry', 'Older entry']);
    }
}
