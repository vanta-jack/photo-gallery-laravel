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
        $response->assertViewHas('topRatedPhotos', function ($photos) use ($photoA, $photoB, $photoC): bool {
            return $photos->pluck('id')->values()->all() === [$photoA->id, $photoB->id, $photoC->id];
        });
        $response->assertViewHas('mostCommentedPhotos', function ($photos) use ($photoA, $photoB, $photoC): bool {
            return $photos->pluck('id')->values()->all() === [$photoC->id, $photoA->id, $photoB->id];
        });
        $response->assertSeeText('Sunset Vista');
        $response->assertSeeText('Forest Trail');
        $response->assertSeeText('5.0/5');
        $response->assertSeeText('4 comments');
    }
}
