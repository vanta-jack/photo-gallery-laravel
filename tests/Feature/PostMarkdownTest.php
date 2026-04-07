<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
}
