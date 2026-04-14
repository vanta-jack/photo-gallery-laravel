<?php

namespace Tests\Feature;

use App\Livewire\Markdown\Preview;
use Livewire\Livewire;
use Tests\TestCase;

class MarkdownPreviewLivewireTest extends TestCase
{
    public function test_preview_renders_safe_markdown_from_initial_content(): void
    {
        Livewire::test(Preview::class, [
            'content' => '**Preview Bold** <script>alert(1)</script>',
            'previewId' => 'preview-1',
        ])
            ->assertSee('<strong>Preview Bold</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_preview_updates_only_for_matching_id(): void
    {
        Livewire::test(Preview::class, [
            'content' => '**Initial**',
            'previewId' => 'preview-1',
        ])
            ->dispatch('markdown-preview:update', previewId: 'preview-1', content: '**Updated**')
            ->assertSee('<strong>Updated</strong>', false)
            ->dispatch('markdown-preview:update', previewId: 'other', content: '**Ignored**')
            ->assertDontSee('<strong>Ignored</strong>', false)
            ->assertSee('<strong>Updated</strong>', false);
    }
}
