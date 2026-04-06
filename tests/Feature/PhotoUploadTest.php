<?php

namespace Tests\Feature;

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
        return 'data:image/webp;base64,' . base64_encode('fake-webp');
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
    }

    public function test_multiple_photo_upload_is_rejected(): void
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

        $response->assertSessionHasErrors('photo');
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
}
