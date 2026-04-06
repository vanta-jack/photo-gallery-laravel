<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlbumCreateTest extends TestCase
{
    use RefreshDatabase;

    private function fakeWebpDataUri(): string
    {
        return 'data:image/webp;base64,' . base64_encode('album-create-upload-webp');
    }

    public function test_album_create_modal_upload_returns_json_photo_for_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->postJson(route('albums.photos.create.store'), [
                'photo' => $this->fakeWebpDataUri(),
                'title' => 'Create Modal Upload',
                'description' => 'Inline upload on create page',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('photo.title', 'Create Modal Upload');

        $photo = Photo::query()->find((int) $response->json('photo.id'));

        $this->assertNotNull($photo);
        $this->assertSame($user->id, $photo->user_id);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_album_create_modal_upload_requires_authentication(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('albums.photos.create.store'), [
            'photo' => $this->fakeWebpDataUri(),
            'title' => 'Guest blocked',
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('photos', 0);
    }
}
