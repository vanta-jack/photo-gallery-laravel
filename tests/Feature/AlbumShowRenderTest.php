<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlbumShowRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_album_show_renders_for_guest_without_blade_parse_errors(): void
    {
        $owner = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create(['is_private' => false]);
        $photo = Photo::factory()->for($owner)->create(['title' => 'Regression Photo']);
        $album->photos()->attach($photo);

        $response = $this->get(route('albums.show', ['album' => $album]));

        $response->assertOk();
        $response->assertViewIs('albums.show');
        $response->assertSeeText($album->title);
        $response->assertSeeText('Regression Photo');
        $response->assertSeeText('Average rating');
    }

    public function test_private_album_show_redirects_guest_to_home(): void
    {
        $owner = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create(['is_private' => true]);

        $response = $this->get(route('albums.show', ['album' => $album]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'This album is private.');
    }
}
