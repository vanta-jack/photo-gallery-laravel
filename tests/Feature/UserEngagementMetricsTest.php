<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserEngagementMetricsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_profile_renders_engagement_summary_top_content_and_trends(): void
    {
        Carbon::setTestNow('2026-04-15 12:00:00');

        try {
            $user = User::factory()->user()->create([
                'first_name' => 'Metrics',
                'last_name' => 'Owner',
            ]);

            $topPost = Post::factory()->for($user)->create(['title' => 'Most Voted Story']);
            $secondaryPost = Post::factory()->for($user)->create(['title' => 'Less Voted Story']);

            $topPhoto = Photo::factory()->for($user)->create(['title' => 'Community Favorite Photo']);
            $secondaryPhoto = Photo::factory()->for($user)->create(['title' => 'Quiet Photo']);

            $actors = User::factory()->count(12)->user()->create();

            PostVote::factory()->for($topPost)->for($actors[0])->create([
                'created_at' => '2026-04-02 09:00:00',
                'updated_at' => '2026-04-02 09:00:00',
            ]);
            PostVote::factory()->for($topPost)->for($actors[1])->create([
                'created_at' => '2026-04-06 09:00:00',
                'updated_at' => '2026-04-06 09:00:00',
            ]);
            PostVote::factory()->for($topPost)->for($actors[2])->create([
                'created_at' => '2026-03-08 09:00:00',
                'updated_at' => '2026-03-08 09:00:00',
            ]);
            PostVote::factory()->for($secondaryPost)->for($actors[3])->create([
                'created_at' => '2026-02-08 09:00:00',
                'updated_at' => '2026-02-08 09:00:00',
            ]);

            PhotoComment::factory()->for($topPhoto)->for($actors[4])->create([
                'body' => 'Amazing shot!',
                'created_at' => '2026-04-07 09:00:00',
                'updated_at' => '2026-04-07 09:00:00',
            ]);
            PhotoComment::factory()->for($topPhoto)->for($actors[5])->create([
                'body' => 'Love this.',
                'created_at' => '2026-03-07 09:00:00',
                'updated_at' => '2026-03-07 09:00:00',
            ]);
            PhotoComment::factory()->for($secondaryPhoto)->for($actors[6])->create([
                'body' => 'Great work.',
                'created_at' => '2026-01-07 09:00:00',
                'updated_at' => '2026-01-07 09:00:00',
            ]);

            PhotoRating::factory()->for($topPhoto)->for($actors[7])->create([
                'rating' => 5,
                'created_at' => '2026-04-05 09:00:00',
                'updated_at' => '2026-04-05 09:00:00',
            ]);
            PhotoRating::factory()->for($topPhoto)->for($actors[8])->create([
                'rating' => 4,
                'created_at' => '2026-04-09 09:00:00',
                'updated_at' => '2026-04-09 09:00:00',
            ]);
            PhotoRating::factory()->for($topPhoto)->for($actors[9])->create([
                'rating' => 4,
                'created_at' => '2026-03-09 09:00:00',
                'updated_at' => '2026-03-09 09:00:00',
            ]);
            PhotoRating::factory()->for($topPhoto)->for($actors[10])->create([
                'rating' => 3,
                'created_at' => '2026-02-09 09:00:00',
                'updated_at' => '2026-02-09 09:00:00',
            ]);
            PhotoRating::factory()->for($secondaryPhoto)->for($actors[11])->create([
                'rating' => 5,
                'created_at' => '2026-04-11 09:00:00',
                'updated_at' => '2026-04-11 09:00:00',
            ]);

            $response = $this->get(route('users.show', $user));

            $response->assertOk();
            $response->assertSeeText('Engagement Metrics');
            $response->assertSeeText('Most Voted Story');
            $response->assertSeeText('3 total votes');
            $response->assertSeeText('Community Favorite Photo');
            $response->assertSeeText('4 ratings • 4.0/5 average');
            $response->assertSeeText('Post Votes');
            $response->assertSeeText('Photo Comments');
            $response->assertSeeText('Photo Ratings');
            $response->assertSeeText('Apr 2026');
            $response->assertSeeText('6 interactions');
            $response->assertSeeText('2 votes, 1 comments, 3 ratings');
        } finally {
            Carbon::setTestNow();
        }
    }
}
