<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostMarkdownTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_post_forms_bootstrap_markdown_editor(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('posts.create'))
            ->assertOk()
            ->assertSee('data-markdown-editor', false)
            ->assertSee('data-markdown-toolbar', false);

        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('posts.edit', $post))
            ->assertOk()
            ->assertSee('data-markdown-editor', false)
            ->assertSee('data-markdown-toolbar', false);
    }

    public function test_post_show_renders_markdown_html_safely(): void
    {
        $user = User::factory()->user()->create();
        $post = Post::factory()->for($user)->create([
            'title' => 'Markdown Post',
            'description' => '**Bold Text** <script>alert(1)</script>',
        ]);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('Bold Text')
            ->assertSee('<strong>Bold Text</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_post_views_show_author_avatar_when_profile_photo_exists(): void
    {
        $user = User::factory()->user()->create([
            'first_name' => 'Ava',
            'last_name' => 'Stone',
        ]);
        $avatarPhoto = Photo::factory()->for($user)->create([
            'path' => 'photos/avatars/ava-stone.jpg',
        ]);
        $user->update(['profile_photo_id' => $avatarPhoto->id]);

        $post = Post::factory()->for($user)->create([
            'title' => 'Avatar Post',
        ]);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSee(Storage::url($avatarPhoto->path), false);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee(Storage::url($avatarPhoto->path), false);
    }

    public function test_post_views_show_author_initials_when_profile_photo_missing(): void
    {
        $user = User::factory()->user()->create([
            'first_name' => 'Noah',
            'last_name' => 'Mills',
            'profile_photo_id' => null,
        ]);
        $post = Post::factory()->for($user)->create([
            'title' => 'Initials Post',
        ]);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSeeText('NM');

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSeeText('NM');
    }

    public function test_post_edit_marks_legacy_main_photo_as_selected_attachment(): void
    {
        $user = User::factory()->user()->create();
        $mainPhoto = Photo::factory()->for($user)->create();
        $post = Post::factory()->for($user)->create([
            'photo_id' => $mainPhoto->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('posts.edit', $post))
            ->assertOk();

        $this->assertMatchesRegularExpression(
            '/name="photo_ids\[\]"\s+value="'.$mainPhoto->id.'"\s+data-photo-existing-id="'.$mainPhoto->id.'"\s+checked/s',
            $response->getContent(),
        );
    }

    public function test_post_show_falls_back_to_attached_photos_when_main_photo_is_missing(): void
    {
        $user = User::factory()->user()->create();
        $firstAttachment = Photo::factory()->for($user)->create(['path' => 'photos/posts/fallback-first.webp']);
        $secondAttachment = Photo::factory()->for($user)->create(['path' => 'photos/posts/fallback-second.webp']);

        $post = Post::factory()->for($user)->create([
            'title' => 'Fallback attachments post',
            'photo_id' => null,
        ]);
        $post->photos()->sync([$firstAttachment->id, $secondAttachment->id]);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSeeText('Attached photos')
            ->assertSeeText('Main image')
            ->assertSee(Storage::url($firstAttachment->path), false)
            ->assertSee(Storage::url($secondAttachment->path), false);
    }

    public function test_photo_tracks_main_post_relation_separately_from_attachments(): void
    {
        $user = User::factory()->user()->create();
        $mainPhoto = Photo::factory()->for($user)->create();
        $secondaryPhoto = Photo::factory()->for($user)->create();

        $post = Post::factory()->for($user)->create([
            'photo_id' => $mainPhoto->id,
        ]);

        $post->photos()->sync([$mainPhoto->id, $secondaryPhoto->id]);

        $this->assertTrue($mainPhoto->posts()->whereKey($post->id)->exists());
        $this->assertTrue($mainPhoto->postAttachments()->whereKey($post->id)->exists());
        $this->assertTrue($secondaryPhoto->postAttachments()->whereKey($post->id)->exists());
        $this->assertFalse($secondaryPhoto->posts()->whereKey($post->id)->exists());
    }
}
