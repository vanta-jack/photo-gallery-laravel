<?php

namespace Tests\Feature;

use App\Livewire\Markdown\Preview;
use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarkdownEditorCoverageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_markdown_editor_component_is_present_on_all_in_scope_forms(): void
    {
        $user = User::factory()->user()->create();

        $post = Post::factory()->for($user)->create();
        $album = Album::factory()->for($user)->create();
        $photo = Photo::factory()->for($user)->create();
        $milestone = Milestone::factory()->for($user)->create(['photo_id' => $photo->id]);

        $guestbookPost = Post::factory()->for($user)->create();
        $guestbookEntry = GuestbookEntry::factory()->create([
            'post_id' => $guestbookPost->id,
            'photo_id' => $photo->id,
        ]);

        $this->actingAs($user)->get(route('posts.create'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('posts.edit', $post))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('milestones.create'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('milestones.edit', $milestone))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('albums.create'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('albums.edit', $album))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('photos.create'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('photos.edit', $photo))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->get(route('guestbook.create'))->assertOk()->assertSee('data-markdown-editor', false);
        $this->actingAs($user)->get(route('guestbook.edit', $guestbookEntry))->assertOk()->assertSee('data-markdown-editor', false);
    }

    public function test_album_and_photo_views_render_markdown_descriptions_safely(): void
    {
        $user = User::factory()->user()->create();

        $album = Album::factory()->for($user)->create([
            'description' => '**Album Bold** <script>alert(1)</script>',
        ]);

        $photo = Photo::factory()->for($user)->create([
            'description' => '**Photo Bold** <script>alert(1)</script>',
        ]);

        $this->actingAs($user)
            ->get(route('albums.index'))
            ->assertOk()
            ->assertSee('<strong>Album Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->actingAs($user)
            ->get(route('albums.show', $album))
            ->assertOk()
            ->assertSee('<strong>Album Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->actingAs($user)
            ->get(route('photos.index'))
            ->assertOk()
            ->assertSee('<strong>Photo Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->get(route('photos.show', $photo))
            ->assertOk()
            ->assertSee('<strong>Photo Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_guestbook_feed_renders_markdown_description_safely(): void
    {
        $user = User::factory()->user()->create();

        $post = Post::factory()->for($user)->create([
            'title' => 'Guestbook Markdown',
            'description' => '**Guestbook Bold** <script>alert(1)</script>',
        ]);

        GuestbookEntry::factory()->create([
            'post_id' => $post->id,
            'photo_id' => null,
        ]);

        $this->get(route('guestbook.index'))
            ->assertOk()
            ->assertSee('<strong>Guestbook Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_livewire_markdown_preview_renders_safe_markdown(): void
    {
        Livewire::test(Preview::class, [
            'content' => '',
            'previewId' => 'preview-safe',
        ])
            ->dispatch('markdown-preview:update', previewId: 'preview-safe', content: '**Preview Bold** <script>alert(1)</script>')
            ->assertSee('<strong>Preview Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_livewire_markdown_preview_renders_headings_lists_and_blockquotes(): void
    {
        $markdown = <<<'MD'
        ## Heading Preview

        - First item
        - Second item

        1. One
        2. Two

        > Quoted text
        MD;

        Livewire::test(Preview::class, [
            'content' => '',
            'previewId' => 'preview-structure',
        ])
            ->dispatch('markdown-preview:update', previewId: 'preview-structure', content: $markdown)
            ->assertSee('<h2>Heading Preview</h2>', false)
            ->assertSee('<ul>', false)
            ->assertSee('<li>First item</li>', false)
            ->assertSee('<ol>', false)
            ->assertSee('<li>One</li>', false)
            ->assertSee('<blockquote>', false)
            ->assertSee('<p>Quoted text</p>', false);
    }

    public function test_markdown_editor_renders_explicit_toolbar_buttons_with_theme_classes(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('posts.create'))
            ->assertOk()
            ->assertSee('data-md-action="bold"', false)
            ->assertSee('data-md-action="italic"', false)
            ->assertSee('data-md-action="heading"', false)
            ->assertSee('data-md-action="unordered-list"', false)
            ->assertSee('data-md-action="ordered-list"', false)
            ->assertSee('data-md-action="link"', false)
            ->assertSee('data-md-action="code"', false)
            ->assertDontSee('onclick="return window.applyMarkdownToolbarAction(event)"', false)
            ->assertDontSee('ontouchstart="return window.applyMarkdownToolbarAction(event)"', false)
            ->assertSee('bg-secondary', false)
            ->assertSee('text-foreground', false)
            ->assertSee('border-border', false);
    }

    public function test_profile_professional_experience_description_renders_markdown_safely(): void
    {
        $user = User::factory()->user()->create([
            'professional_experience' => [
                [
                    'title' => 'Engineer',
                    'company' => 'Acme',
                    'start_date' => '2023-01',
                    'end_date' => null,
                    'description' => '## Career Highlights',
                ],
            ],
        ]);

        $this->get(route('users.show', $user))
            ->assertOk()
            ->assertSee('<h2>Career Highlights</h2>', false);
    }
}
