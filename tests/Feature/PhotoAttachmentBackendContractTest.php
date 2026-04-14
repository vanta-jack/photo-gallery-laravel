<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoAttachmentBackendContractTest extends TestCase
{
    use RefreshDatabase;

    private function webpPayload(string $content = 'guestbook-upload'): string
    {
        return 'data:image/webp;base64,'.base64_encode($content);
    }

    public function test_guestbook_create_and_update_support_legacy_photo_id_payload(): void
    {
        $user = User::factory()->user()->create();
        $firstPhoto = Photo::factory()->for($user)->create();
        $secondPhoto = Photo::factory()->for($user)->create();

        $storeResponse = $this->actingAs($user)->post(route('guestbook.store'), [
            'title' => 'Legacy guestbook entry',
            'description' => 'Legacy photo_id create payload',
            'photo_id' => $firstPhoto->id,
        ]);

        $entry = GuestbookEntry::query()->first();

        $this->assertNotNull($entry);
        $storeResponse->assertRedirect(route('guestbook.index'));
        $this->assertSame($firstPhoto->id, $entry->photo_id);
        $this->assertSame([$firstPhoto->id], $entry->photos()->pluck('photos.id')->all());

        $updateResponse = $this->actingAs($user)->put(route('guestbook.update', $entry), [
            'title' => 'Updated legacy guestbook entry',
            'description' => 'Legacy photo_id update payload',
            'photo_id' => $secondPhoto->id,
        ]);

        $entry->refresh();

        $updateResponse->assertRedirect(route('guestbook.index'));
        $this->assertSame($secondPhoto->id, $entry->photo_id);
        $this->assertSame([$secondPhoto->id], $entry->photos()->pluck('photos.id')->all());
    }

    public function test_guest_cannot_attach_existing_photo_ids_when_creating_guestbook_entry(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->for($user)->create();

        $response = $this->post(route('guestbook.store'), [
            'title' => 'Anonymous entry',
            'description' => 'Trying to attach someone else photo',
            'photo_ids' => [$photo->id],
            'main_photo_pick' => 'existing:'.$photo->id,
        ]);

        $response->assertSessionHasErrors(['photo_ids']);
        $this->assertDatabaseCount('guestbook_entries', 0);
    }

    public function test_authenticated_user_can_mix_existing_and_uploaded_guestbook_photos_and_pick_uploaded_main(): void
    {
        $user = User::factory()->user()->create();
        $existingPhoto = Photo::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('guestbook.store'), [
            'title' => 'Mixed photos guestbook entry',
            'description' => 'Entry with mixed attachments',
            'photo_ids' => [$existingPhoto->id],
            'photos' => [$this->webpPayload('guestbook-mixed-upload')],
            'main_photo_pick' => 'upload:0',
        ]);

        $entry = GuestbookEntry::query()->with(['post', 'photos'])->first();

        $this->assertNotNull($entry);
        $response->assertRedirect(route('guestbook.index'));
        $this->assertSame($user->id, $entry->post->user_id);

        $uploadedPhoto = $entry->photos->firstWhere('id', '!=', $existingPhoto->id);
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($user->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $entry->photo_id);
        $this->assertSame(
            [$existingPhoto->id, $uploadedPhoto->id],
            $entry->photos->pluck('id')->sort()->values()->all(),
        );
    }

    public function test_admin_updating_guest_authored_entry_without_attachment_changes_keeps_existing_guestbook_photos(): void
    {
        $admin = User::factory()->admin()->create();
        $guestUploader = User::factory()->guest()->create();
        $mainPhoto = Photo::factory()->for($guestUploader)->create();
        $secondaryPhoto = Photo::factory()->for($guestUploader)->create();
        $post = Post::factory()->create([
            'user_id' => null,
            'title' => 'Guest post',
            'description' => 'Guest description',
        ]);
        $entry = GuestbookEntry::factory()->for($post, 'post')->create([
            'photo_id' => $mainPhoto->id,
        ]);
        $entry->photos()->sync([$mainPhoto->id, $secondaryPhoto->id]);

        $response = $this->actingAs($admin)->put(route('guestbook.update', $entry), [
            'title' => 'Updated by admin',
            'description' => 'Updated copy',
            'main_photo_pick' => 'existing:'.$mainPhoto->id,
        ]);

        $entry->refresh();

        $response->assertRedirect(route('guestbook.index'));
        $this->assertSame($mainPhoto->id, $entry->photo_id);
        $this->assertSame(
            [$mainPhoto->id, $secondaryPhoto->id],
            $entry->photos()->orderBy('photos.id')->pluck('photos.id')->all(),
        );
        $this->assertNull($entry->post->fresh()->user_id);
    }

    public function test_admin_uploaded_guestbook_photos_are_attributed_to_entry_owner_on_update(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->user()->create();
        $post = Post::factory()->for($owner)->create();
        $entry = GuestbookEntry::factory()->for($post, 'post')->create([
            'photo_id' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('guestbook.update', $entry), [
            'title' => 'Admin-edited title',
            'description' => 'Admin-edited description',
            'photos' => [$this->webpPayload('admin-owner-attribution')],
            'main_photo_pick' => 'upload:0',
        ]);

        $entry->refresh();
        $uploadedPhoto = $entry->photos()->first();

        $response->assertRedirect(route('guestbook.index'));
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($owner->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $entry->photo_id);
        $this->assertSame($owner->id, $entry->post->fresh()->user_id);
    }

    public function test_post_create_rejects_foreign_existing_photo_ids(): void
    {
        $author = User::factory()->user()->create();
        $otherUser = User::factory()->user()->create();
        $foreignPhoto = Photo::factory()->for($otherUser)->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Unauthorized photo usage',
            'description' => 'Should fail ownership validation',
            'photo_ids' => [$foreignPhoto->id],
            'main_photo_pick' => 'existing:'.$foreignPhoto->id,
        ]);

        $response->assertSessionHasErrors(['photo_ids']);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_post_create_supports_mixed_attachments_and_uploaded_main_photo_pick(): void
    {
        $user = User::factory()->user()->create();
        $existingPhoto = Photo::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Mixed attachment post',
            'description' => 'Post with existing and uploaded photos',
            'photo_ids' => [$existingPhoto->id],
            'photos' => [$this->webpPayload('post-mixed-upload')],
            'main_photo_pick' => 'upload:0',
        ]);

        $post = Post::query()->with('photos')->first();

        $this->assertNotNull($post);
        $response->assertRedirect(route('posts.show', $post));

        $uploadedPhoto = $post->photos->firstWhere('id', '!=', $existingPhoto->id);
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($user->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $post->photo_id);
        $this->assertSame(
            [$existingPhoto->id, $uploadedPhoto->id],
            $post->photos->pluck('id')->sort()->values()->all(),
        );
    }

    public function test_admin_uploaded_post_photos_are_attributed_to_post_owner_on_update(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->user()->create();
        $post = Post::factory()->for($owner)->create([
            'photo_id' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Admin-updated post title',
            'description' => 'Admin-updated post description',
            'photos' => [$this->webpPayload('admin-post-owner-attribution')],
            'main_photo_pick' => 'upload:0',
        ]);

        $post->refresh();
        $uploadedPhoto = $post->photos()->first();

        $response->assertRedirect(route('posts.show', $post));
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($owner->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $post->photo_id);
    }

    public function test_post_create_and_update_support_legacy_photo_id_payload(): void
    {
        $user = User::factory()->user()->create();
        $firstPhoto = Photo::factory()->for($user)->create();
        $secondPhoto = Photo::factory()->for($user)->create();

        $storeResponse = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Legacy post',
            'description' => 'Legacy photo_id create payload',
            'photo_id' => $firstPhoto->id,
        ]);

        $post = Post::query()->first();

        $this->assertNotNull($post);
        $storeResponse->assertRedirect(route('posts.show', $post));
        $this->assertSame($firstPhoto->id, $post->photo_id);
        $this->assertSame([$firstPhoto->id], $post->photos()->pluck('photos.id')->all());

        $updateResponse = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => 'Updated legacy post',
            'description' => 'Legacy photo_id update payload',
            'photo_id' => $secondPhoto->id,
        ]);

        $post->refresh();

        $updateResponse->assertRedirect(route('posts.show', $post));
        $this->assertSame($secondPhoto->id, $post->photo_id);
        $this->assertSame([$secondPhoto->id], $post->photos()->pluck('photos.id')->all());
    }

    public function test_album_create_supports_mixed_attachments_and_uploaded_cover_pick(): void
    {
        $user = User::factory()->user()->create();
        $existingPhoto = Photo::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('albums.store'), [
            'title' => 'Mixed attachment album',
            'description' => 'Album with existing and uploaded photos',
            'is_private' => false,
            'photo_ids' => [$existingPhoto->id],
            'photos' => [$this->webpPayload('album-mixed-upload')],
            'main_photo_pick' => 'upload:0',
        ]);

        $album = Album::query()->with('photos')->first();

        $this->assertNotNull($album);
        $response->assertRedirect(route('albums.show', $album));

        $uploadedPhoto = $album->photos->firstWhere('id', '!=', $existingPhoto->id);
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($user->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $album->cover_photo_id);
        $this->assertSame(
            [$existingPhoto->id, $uploadedPhoto->id],
            $album->photos->pluck('id')->sort()->values()->all(),
        );
    }

    public function test_admin_uploaded_album_photos_are_attributed_to_album_owner_on_update(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->user()->create();
        $album = Album::factory()->for($owner)->create([
            'cover_photo_id' => null,
            'is_private' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('albums.update', $album), [
            'title' => 'Admin-updated album title',
            'description' => 'Admin-updated album description',
            'is_private' => false,
            'photos' => [$this->webpPayload('admin-album-owner-attribution')],
            'main_photo_pick' => 'upload:0',
        ]);

        $album->refresh();
        $uploadedPhoto = $album->photos()->first();

        $response->assertRedirect(route('albums.show', $album));
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($owner->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $album->cover_photo_id);
    }

    public function test_album_create_and_update_accept_legacy_cover_photo_payload_without_photo_ids(): void
    {
        $user = User::factory()->user()->create();
        $firstPhoto = Photo::factory()->for($user)->create();
        $secondPhoto = Photo::factory()->for($user)->create();

        $storeResponse = $this->actingAs($user)->post(route('albums.store'), [
            'title' => 'Legacy album',
            'description' => 'Legacy cover_photo_id create payload',
            'cover_photo_id' => $firstPhoto->id,
            'is_private' => false,
        ]);

        $album = Album::query()->first();

        $this->assertNotNull($album);
        $storeResponse->assertRedirect(route('albums.show', $album));
        $this->assertSame($firstPhoto->id, $album->cover_photo_id);
        $this->assertSame([$firstPhoto->id], $album->photos()->pluck('photos.id')->all());

        $updateResponse = $this->actingAs($user)->put(route('albums.update', $album), [
            'title' => 'Updated legacy album',
            'description' => 'Legacy cover_photo_id update payload',
            'cover_photo_id' => $secondPhoto->id,
            'is_private' => false,
        ]);

        $album->refresh();

        $updateResponse->assertRedirect(route('albums.show', $album));
        $this->assertSame($secondPhoto->id, $album->cover_photo_id);
        $this->assertSame([$secondPhoto->id], $album->photos()->pluck('photos.id')->all());
    }

    public function test_milestone_create_rejects_foreign_existing_photo_ids(): void
    {
        $author = User::factory()->user()->create();
        $otherUser = User::factory()->user()->create();
        $foreignPhoto = Photo::factory()->for($otherUser)->create();

        $response = $this->actingAs($author)->post(route('milestones.store'), [
            'stage' => 'baby',
            'label' => 'Unauthorized milestone photo',
            'photo_ids' => [$foreignPhoto->id],
            'main_photo_pick' => 'existing:'.$foreignPhoto->id,
        ]);

        $response->assertSessionHasErrors(['photo_ids']);
        $this->assertDatabaseCount('milestones', 0);
    }

    public function test_milestone_create_supports_mixed_attachments_and_uploaded_main_photo_pick(): void
    {
        $user = User::factory()->user()->create();
        $existingPhoto = Photo::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(route('milestones.store'), [
            'stage' => 'grade_school',
            'label' => 'Mixed attachment milestone',
            'photo_ids' => [$existingPhoto->id],
            'photos' => [$this->webpPayload('milestone-mixed-upload')],
            'main_photo_pick' => 'upload:0',
        ]);

        $milestone = Milestone::query()->with('photos')->first();

        $this->assertNotNull($milestone);
        $response->assertRedirect(route('milestones.show', $milestone));

        $uploadedPhoto = $milestone->photos->firstWhere('id', '!=', $existingPhoto->id);
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($user->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $milestone->photo_id);
        $this->assertSame(
            [$existingPhoto->id, $uploadedPhoto->id],
            $milestone->photos->pluck('id')->sort()->values()->all(),
        );
    }

    public function test_admin_uploaded_milestone_photos_are_attributed_to_milestone_owner_on_update(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->user()->create();
        $mainPhoto = Photo::factory()->for($owner)->create();
        $milestone = Milestone::factory()->for($owner)->create([
            'photo_id' => $mainPhoto->id,
            'stage' => 'grade_school',
        ]);
        $milestone->photos()->sync([$mainPhoto->id]);

        $response = $this->actingAs($admin)->put(route('milestones.update', $milestone), [
            'label' => 'Admin-updated milestone label',
            'photos' => [$this->webpPayload('admin-milestone-owner-attribution')],
            'main_photo_pick' => 'upload:0',
        ]);

        $milestone->refresh();
        $uploadedPhoto = $milestone->photos()->first();

        $response->assertRedirect(route('milestones.show', $milestone));
        $this->assertNotNull($uploadedPhoto);
        $this->assertSame($owner->id, $uploadedPhoto->user_id);
        $this->assertSame($uploadedPhoto->id, $milestone->photo_id);
    }

    public function test_milestone_update_without_attachment_payload_keeps_existing_photo_links(): void
    {
        $user = User::factory()->user()->create();
        $mainPhoto = Photo::factory()->for($user)->create();
        $secondaryPhoto = Photo::factory()->for($user)->create();
        $milestone = Milestone::factory()->for($user)->create([
            'photo_id' => $mainPhoto->id,
            'stage' => 'grade_school',
            'label' => 'Initial milestone',
        ]);
        $milestone->photos()->sync([$mainPhoto->id, $secondaryPhoto->id]);

        $response = $this->actingAs($user)->put(route('milestones.update', $milestone), [
            'label' => 'Updated milestone label',
        ]);

        $milestone->refresh();

        $response->assertRedirect(route('milestones.show', $milestone));
        $this->assertSame($mainPhoto->id, $milestone->photo_id);
        $this->assertSame(
            [$mainPhoto->id, $secondaryPhoto->id],
            $milestone->photos()->orderBy('photos.id')->pluck('photos.id')->all(),
        );
    }
}
