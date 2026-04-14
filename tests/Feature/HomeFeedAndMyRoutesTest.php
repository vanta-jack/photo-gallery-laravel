<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeFeedAndMyRoutesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_my_routes_require_authentication(): void
    {
        $this->get(route('photos.index'))->assertRedirect(route('login'));
        $this->get(route('albums.index'))->assertRedirect(route('login'));
        $this->get(route('posts.index'))->assertRedirect(route('login'));
    }

    public function test_my_routes_only_show_authenticated_user_content(): void
    {
        $owner = User::factory()->user()->create();
        $other = User::factory()->user()->create();

        Post::factory()->for($owner)->create(['title' => 'Owner Post']);
        Post::factory()->for($other)->create(['title' => 'Other Post']);

        Album::factory()->for($owner)->create(['title' => 'Owner Album']);
        Album::factory()->for($other)->create(['title' => 'Other Album']);

        $this->actingAs($owner)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSeeText('Owner Post')
            ->assertDontSeeText('Other Post');

        $this->actingAs($owner)
            ->get(route('albums.index'))
            ->assertOk()
            ->assertSeeText('Owner Album')
            ->assertDontSeeText('Other Album');
    }

    public function test_home_feed_only_shows_public_milestones_and_respects_type_filter(): void
    {
        $user = User::factory()->user()->create();

        Milestone::factory()->for($user)->create([
            'label' => 'Public Milestone',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Milestone::factory()->for($user)->create([
            'label' => 'Private Milestone',
            'photo_id' => null,
            'is_public' => false,
        ]);

        Post::factory()->for($user)->create(['title' => 'Public Post']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Public Milestone')
            ->assertDontSeeText('Private Milestone')
            ->assertSeeText('Public Post');

        $this->get(route('home', ['type' => 'milestone']))
            ->assertOk()
            ->assertSeeText('Public Milestone')
            ->assertDontSeeText('Public Post');
    }

    public function test_home_feed_activity_cards_show_author_avatar_or_fallback_initials(): void
    {
        $withAvatar = User::factory()->user()->create([
            'first_name' => 'Iris',
            'last_name' => 'West',
        ]);
        $avatarPhoto = Photo::factory()->for($withAvatar)->create([
            'path' => 'photos/avatars/iris-west.jpg',
        ]);
        $withAvatar->update(['profile_photo_id' => $avatarPhoto->id]);
        Post::factory()->for($withAvatar)->create(['title' => 'Avatar Feed Post']);

        $withoutAvatar = User::factory()->user()->create([
            'first_name' => 'Maya',
            'last_name' => 'Nash',
            'profile_photo_id' => null,
        ]);
        Post::factory()->for($withoutAvatar)->create(['title' => 'Fallback Feed Post']);

        $this->get(route('home', ['type' => 'post']))
            ->assertOk()
            ->assertSeeText('Avatar Feed Post')
            ->assertSeeText('Fallback Feed Post')
            ->assertSee(Storage::url($avatarPhoto->path), false)
            ->assertSeeText('MN');
    }

    public function test_home_feed_post_cards_support_main_photo_and_attachment_fallback_paths(): void
    {
        $user = User::factory()->user()->create();

        $attachmentOnlyPhoto = Photo::factory()->for($user)->create([
            'path' => 'photos/feed/attachment-fallback.webp',
        ]);
        $absoluteMainPhoto = Photo::factory()->for($user)->create([
            'path' => 'https://cdn.example.test/photos/feed/main-photo.webp',
        ]);

        $attachmentFallbackPost = Post::factory()->for($user)->create([
            'title' => 'Attachment fallback post',
            'photo_id' => null,
        ]);
        $attachmentFallbackPost->photos()->sync([$attachmentOnlyPhoto->id]);

        Post::factory()->for($user)->create([
            'title' => 'Absolute path main photo post',
            'photo_id' => $absoluteMainPhoto->id,
        ]);

        $this->get(route('home', ['type' => 'post']))
            ->assertOk()
            ->assertSeeText('Attachment fallback post')
            ->assertSeeText('Absolute path main photo post')
            ->assertSee(Storage::url($attachmentOnlyPhoto->path), false)
            ->assertSee($absoluteMainPhoto->path, false);
    }
}
