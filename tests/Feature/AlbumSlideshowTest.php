<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AlbumSlideshowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_album_show_renders_slideshow_modal()
    {
        $user = User::factory()->create();
        $album = Album::factory()->for($user)->create(['is_private' => false]);
        Photo::factory(3)->for($user)->create()->each(function ($photo) use ($album) {
            $album->photos()->attach($photo);
        });

        $response = $this->actingAs($user)->get(route('albums.show', $album));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'data-slideshow-open',
            'album-spotlight-modal',
            'data-slideshow-root',
        ]);
    }

    public function test_album_slideshow_contains_all_required_elements()
    {
        $user = User::factory()->create();
        $album = Album::factory()->for($user)->create(['is_private' => false]);
        Photo::factory(2)->for($user)->create()->each(function ($photo) use ($album) {
            $album->photos()->attach($photo);
        });

        $response = $this->actingAs($user)->get(route('albums.show', $album));

        // Check for all required data attributes
        $required = [
            'data-slideshow-open' => 'Open button',
            'data-slideshow-root' => 'Modal root',
            'data-slideshow-image' => 'Image element',
            'data-slideshow-title' => 'Title element',
            'data-slideshow-description' => 'Description element',
            'data-slideshow-counter' => 'Counter element',
            'data-slideshow-date' => 'Date element',
            'data-slideshow-detail-link' => 'Detail link',
            'data-slideshow-close' => 'Close button',
            'data-slideshow-prev' => 'Previous button',
            'data-slideshow-next' => 'Next button',
            'data-slideshow-toggle-autoplay' => 'Autoplay button',
            'data-slideshow-stage' => 'Stage button',
            'data-slideshow-photos' => 'Photo payload',
        ];

        foreach ($required as $attribute => $label) {
            $response->assertSee($attribute, false);
        }
    }

    public function test_album_slideshow_has_valid_json_payload()
    {
        $user = User::factory()->create();
        $album = Album::factory()->for($user)->create(['is_private' => false]);
        
        $photo1 = Photo::factory()->for($user)->create(['title' => 'Test Photo 1']);
        $photo2 = Photo::factory()->for($user)->create(['title' => 'Test Photo 2']);
        
        $album->photos()->attach([$photo1->id, $photo2->id]);

        $response = $this->actingAs($user)->get(route('albums.show', $album));
        $content = $response->getContent();
        
        // Extract JSON from the script tag
        preg_match('/<script type="application\/json" data-slideshow-photos>(.+?)<\/script>/s', $content, $matches);
        
        $this->assertNotEmpty($matches, 'Could not find slideshow photos JSON script tag');
        
        $json = trim($matches[1]);
        $photos = json_decode($json, true);
        
        $this->assertIsArray($photos, 'JSON payload must be an array');
        $this->assertCount(2, $photos, 'Must have 2 photos');
        
        // Check first photo structure
        $this->assertArrayHasKey('id', $photos[0]);
        $this->assertArrayHasKey('url', $photos[0]);
        $this->assertArrayHasKey('title', $photos[0]);
        $this->assertArrayHasKey('description_html', $photos[0]);
        $this->assertArrayHasKey('created_at', $photos[0]);
        $this->assertArrayHasKey('show_url', $photos[0]);
    }

    public function test_empty_album_does_not_show_slideshow_button()
    {
        $user = User::factory()->create();
        $album = Album::factory()->for($user)->create(['is_private' => false]);
        // Album has no photos

        $response = $this->actingAs($user)->get(route('albums.show', $album));

        // Button should not render if no photos
        $response->assertDontSee('data-slideshow-open', escape: false);
    }
}
