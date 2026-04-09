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
            ->assertSee('data-markdown-editor', false);

        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('posts.edit', $post))
            ->assertOk()
            ->assertSee('data-markdown-editor', false);
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
}
