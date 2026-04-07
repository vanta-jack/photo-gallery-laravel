<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminModerationActionsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_dashboard_shows_accounts_table(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->user()->create(['email' => 'member@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Accounts')
            ->assertSeeText('member@example.com');
    }

    public function test_admin_can_delete_content_from_moderation_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->user()->create();

        $post = Post::factory()->for($author)->create();
        $photo = Photo::factory()->for($author)->create();
        $album = Album::factory()->for($author)->create();
        $milestone = Milestone::factory()->for($author)->create();
        $entryPost = Post::factory()->for($author)->create();
        $guestbook = GuestbookEntry::factory()->for($entryPost, 'post')->create();

        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->delete(route('admin.photos.destroy', $photo))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->delete(route('admin.albums.destroy', $album))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->delete(route('admin.milestones.destroy', $milestone))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->delete(route('admin.guestbook.destroy', $guestbook))->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        $this->assertDatabaseMissing('albums', ['id' => $album->id]);
        $this->assertDatabaseMissing('milestones', ['id' => $milestone->id]);
        $this->assertDatabaseMissing('guestbook_entries', ['id' => $guestbook->id]);
    }

    public function test_non_admin_cannot_hit_moderation_routes(): void
    {
        $user = User::factory()->user()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('admin.posts.destroy', $post))
            ->assertForbidden();
    }
}
