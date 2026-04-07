<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    private function fakeWebpDataUri(): string
    {
        return 'data:image/webp;base64,'.base64_encode('fake-webp');
    }

    private function fakePngDataUri(): string
    {
        return 'data:image/png;base64,'.base64_encode('fake-png');
    }

    private function fakeJpegDataUri(): string
    {
        return 'data:image/jpeg;base64,'.base64_encode('fake-jpeg');
    }

    public function test_single_photo_upload_redirects_to_photo_show(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.store'), [
                'photo' => $this->fakeWebpDataUri(),
                'title' => 'Cover Photo',
                'description' => 'Single upload test',
            ]);

        $photo = Photo::query()->first();

        $this->assertNotNull($photo);
        $response->assertRedirect(route('photos.show', $photo));
        Storage::disk('public')->assertExists($photo->path);
        $this->assertStringStartsWith('photos/', $photo->path);
        $this->assertStringNotContainsString('data:image/', $photo->path);
    }

    public function test_multiple_photo_upload_creates_records_and_redirects_to_index(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.store'), [
                'photos' => [
                    $this->fakeWebpDataUri(),
                    $this->fakeWebpDataUri(),
                ],
                'title' => 'Batch Upload',
                'description' => 'Multiple upload test',
            ]);

        $response->assertRedirect(route('photos.index'));
        $this->assertDatabaseCount('photos', 2);
        Photo::query()->get()->each(fn (Photo $photo) => Storage::disk('public')->assertExists($photo->path));
    }

    public function test_multiple_photo_upload_can_attach_all_photos_to_selected_album(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();
        $album = Album::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.store'), [
                'photos' => [
                    $this->fakeWebpDataUri(),
                    $this->fakePngDataUri(),
                ],
                'album_id' => $album->id,
            ]);

        $response->assertRedirect(route('photos.index'));
        $this->assertDatabaseCount('photos', 2);
        $this->assertDatabaseCount('album_photo', 2);
    }

    public function test_album_assignment_requires_authenticated_owner(): void
    {
        Storage::fake('public');
        $owner = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create();

        $response = $this->post(route('photos.store'), [
            'photo' => $this->fakeWebpDataUri(),
            'album_id' => $album->id,
        ]);

        $response->assertSessionHasErrors('album_id');
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_guest_can_upload_photo_from_any_session(): void
    {
        Storage::fake('public');

        $response = $this->post(route('photos.store'), [
            'photo' => $this->fakeWebpDataUri(),
            'title' => 'Guest Upload',
            'description' => 'Uploaded without authentication',
        ]);

        $photo = Photo::query()->first();

        $this->assertNotNull($photo);
        $response->assertRedirect(route('photos.show', $photo));
        $this->assertDatabaseHas('users', [
            'id' => $photo->user_id,
            'role' => 'guest',
            'email' => null,
        ]);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_png_upload_is_accepted_as_fallback(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.store'), [
                'photo' => $this->fakePngDataUri(),
                'title' => 'PNG Upload',
            ]);

        $photo = Photo::query()->first();

        $this->assertNotNull($photo);
        $response->assertRedirect(route('photos.show', $photo));
        Storage::disk('public')->assertExists($photo->path);
        $this->assertStringEndsWith('.png', $photo->path);
        $this->assertStringNotContainsString('data:image/', $photo->path);
    }

    public function test_jpeg_upload_is_accepted_as_fallback(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.store'), [
                'photo' => $this->fakeJpegDataUri(),
                'title' => 'JPEG Upload',
            ]);

        $photo = Photo::query()->first();

        $this->assertNotNull($photo);
        $response->assertRedirect(route('photos.show', $photo));
        Storage::disk('public')->assertExists($photo->path);
        $this->assertStringEndsWith('.jpg', $photo->path);
        $this->assertStringNotContainsString('data:image/', $photo->path);
    }
}
