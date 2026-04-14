<?php

namespace App\Livewire\Markdown;

use App\Support\MarkdownRenderer;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Preview extends Component
{
    public string $content = '';

    public string $previewId = '';

    public function mount(?string $content = null, ?string $previewId = null): void
    {
        $this->content = (string) ($content ?? '');
        $this->previewId = filled($previewId) ? $previewId : Str::uuid()->toString();
    }

    #[On('markdown-preview:update')]
    public function handlePreviewUpdate(string $previewId, ?string $content = null): void
    {
        if ($previewId !== $this->previewId) {
            return;
        }

        $this->content = (string) ($content ?? '');
    }

    public function render(): View
    {
        return view('livewire.markdown.preview', [
            'previewHtml' => MarkdownRenderer::toSafeHtml($this->content),
        ]);
    }
}
