<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlbumEditTest extends TestCase
{
    use RefreshDatabase;

    private function fakeWebpDataUri(): string
    {
        return 'data:image/webp;base64,' . base64_encode('album-upload-webp');
    }

    public function test_album_update_rejects_non_owner_photos(): void
    {
        $owner = User::factory()->user()->create();
        $otherUser = User::factory()->user()->create();

        $album = Album::factory()->for($owner)->create();
        $ownerPhoto = Photo::factory()->for($owner)->create();
        $foreignPhoto = Photo::factory()->for($otherUser)->create();

        $response = $this
            ->from(route('albums.edit', ['album' => $album]))
            ->actingAs($owner)
            ->put(route('albums.update', ['album' => $album]), [
                'title' => 'Updated album',
                'description' => 'Updated description',
                'is_private' => 0,
                'photo_ids' => [$ownerPhoto->id, $foreignPhoto->id],
                'cover_photo_id' => $foreignPhoto->id,
            ]);

        $response->assertRedirect(route('albums.edit', ['album' => $album]));
        $response->assertSessionHasErrors(['photo_ids.1', 'cover_photo_id']);
        $this->assertDatabaseMissing('album_photo', [
            'album_id' => $album->id,
            'photo_id' => $foreignPhoto->id,
        ]);
    }

    public function test_album_modal_upload_returns_json_photo_for_album_owner(): void
    {
        Storage::fake('public');
        $owner = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create();

        $response = $this
            ->actingAs($owner)
            ->postJson(route('albums.photos.store', ['album' => $album]), [
                'photo' => $this->fakeWebpDataUri(),
                'title' => 'Modal Upload',
                'description' => 'Inline upload',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('photo.title', 'Modal Upload');

        $photoId = (int) $response->json('photo.id');
        $photo = Photo::query()->find($photoId);

        $this->assertNotNull($photo);
        $this->assertSame($owner->id, $photo->user_id);
        Storage::disk('public')->assertExists($photo->path);

        $this->assertDatabaseMissing('album_photo', [
            'album_id' => $album->id,
            'photo_id' => $photo->id,
        ]);
    }

    public function test_album_modal_upload_is_forbidden_for_non_owner(): void
    {
        Storage::fake('public');
        $owner = User::factory()->user()->create();
        $intruder = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create();

        $response = $this
            ->actingAs($intruder)
            ->postJson(route('albums.photos.store', ['album' => $album]), [
                'photo' => $this->fakeWebpDataUri(),
                'title' => 'Blocked upload',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('photos', 0);
    }
}
