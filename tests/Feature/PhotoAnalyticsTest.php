<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PhotoAnalyticsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_photo_analytics_page_is_public_and_renders_ranked_metrics(): void
    {
        $owner = User::factory()->user()->create();

        $photoA = Photo::factory()->for($owner)->create(['title' => 'Sunset Vista']);
        $photoB = Photo::factory()->for($owner)->create(['title' => 'Blue Ocean']);
        $photoC = Photo::factory()->for($owner)->create(['title' => 'Forest Trail']);

        $actors = User::factory()->count(10)->user()->create();

        PhotoRating::factory()->for($photoA)->for($actors[0])->create(['rating' => 5]);
        PhotoRating::factory()->for($photoA)->for($actors[1])->create(['rating' => 5]);
        PhotoRating::factory()->for($photoA)->for($actors[2])->create(['rating' => 5]);

        PhotoRating::factory()->for($photoB)->for($actors[3])->create(['rating' => 5]);
        PhotoRating::factory()->for($photoB)->for($actors[4])->create(['rating' => 4]);

        PhotoRating::factory()->for($photoC)->for($actors[5])->create(['rating' => 3]);

        PhotoComment::factory()->for($photoC)->for($actors[6])->count(4)->create();
        PhotoComment::factory()->for($photoA)->for($actors[7])->count(2)->create();
        PhotoComment::factory()->for($photoB)->for($actors[8])->create();

        $response = $this->get(route('photos.analytics'));

        $response->assertOk();
        $response->assertSeeText('Photo Analytics');
        $response->assertSeeText('Top Rated Photos');
        $response->assertSeeText('Most Commented Photos');
        $response->assertViewHas('scope', 'global');
        $response->assertViewHas('topRatedPhotos', function ($photos) use ($photoA, $photoB, $photoC): bool {
            return $photos->pluck('id')->values()->all() === [$photoA->id, $photoB->id, $photoC->id];
        });
        $response->assertViewHas('mostCommentedPhotos', function ($photos) use ($photoA, $photoB, $photoC): bool {
            return $photos->pluck('id')->values()->all() === [$photoC->id, $photoA->id, $photoB->id];
        });
        $response->assertSeeText('Sunset Vista');
        $response->assertSeeText('Forest Trail');
        $response->assertSeeText('5.0');
        $response->assertSeeText('4 comments');
    }

    public function test_authenticated_user_can_filter_analytics_to_owned_photos(): void
    {
        $owner = User::factory()->user()->create();
        $otherOwner = User::factory()->user()->create();
        $actors = User::factory()->count(8)->user()->create();

        $ownerTop = Photo::factory()->for($owner)->create(['title' => 'Owner Top']);
        $ownerMostCommented = Photo::factory()->for($owner)->create(['title' => 'Owner Most Commented']);
        $globalOnly = Photo::factory()->for($otherOwner)->create(['title' => 'Global Only']);

        PhotoRating::factory()->for($ownerTop)->for($actors[0])->create(['rating' => 5]);
        PhotoRating::factory()->for($ownerTop)->for($actors[1])->create(['rating' => 4]);
        PhotoRating::factory()->for($ownerMostCommented)->for($actors[2])->create(['rating' => 4]);
        PhotoRating::factory()->for($globalOnly)->for($actors[3])->create(['rating' => 5]);
        PhotoRating::factory()->for($globalOnly)->for($actors[4])->create(['rating' => 5]);
        PhotoRating::factory()->for($globalOnly)->for($actors[5])->create(['rating' => 5]);

        PhotoComment::factory()->for($ownerMostCommented)->for($actors[6])->count(3)->create();
        PhotoComment::factory()->for($ownerTop)->for($actors[7])->create();
        PhotoComment::factory()->for($globalOnly)->for($actors[0])->count(5)->create();

        $response = $this->actingAs($owner)->get(route('photos.analytics', ['scope' => 'mine']));

        $response->assertOk();
        $response->assertViewHas('scope', 'mine');
        $response->assertDontSeeText('Global Only');
        $response->assertSeeText('Owner Top');
        $response->assertSeeText('Owner Most Commented');
        $response->assertViewHas('topRatedPhotos', function ($photos) use ($ownerTop, $ownerMostCommented): bool {
            return $photos->pluck('id')->values()->all() === [$ownerTop->id, $ownerMostCommented->id];
        });
        $response->assertViewHas('mostCommentedPhotos', function ($photos) use ($ownerMostCommented, $ownerTop): bool {
            return $photos->pluck('id')->values()->all() === [$ownerMostCommented->id, $ownerTop->id];
        });
    }

    public function test_guest_scope_request_for_mine_falls_back_to_global_results(): void
    {
        $owner = User::factory()->user()->create();
        $anotherOwner = User::factory()->user()->create();
        $actors = User::factory()->count(6)->user()->create();

        $photoA = Photo::factory()->for($owner)->create(['title' => 'Global Alpha']);
        $photoB = Photo::factory()->for($anotherOwner)->create(['title' => 'Global Beta']);

        PhotoRating::factory()->for($photoA)->for($actors[0])->create(['rating' => 5]);
        PhotoRating::factory()->for($photoB)->for($actors[1])->create(['rating' => 4]);
        PhotoComment::factory()->for($photoA)->for($actors[2])->count(2)->create();
        PhotoComment::factory()->for($photoB)->for($actors[3])->count(3)->create();

        $response = $this->get(route('photos.analytics', ['scope' => 'mine']));

        $response->assertOk();
        $response->assertViewHas('scope', 'global');
        $response->assertSeeText('Global Alpha');
        $response->assertSeeText('Global Beta');
    }
}
