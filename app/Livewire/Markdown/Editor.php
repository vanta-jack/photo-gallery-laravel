<?php

namespace App\Livewire\Markdown;

use App\Support\MarkdownRenderer;
use Illuminate\View\View;
use Livewire\Component;

class Editor extends Component
{
    public string $name = 'description';

    public string $label = 'Description';

    public string $content = '';

    public string $placeholder = '';

    public string $help = '';

    public int $rows = 8;

    public bool $required = false;

    public string $id = '';

    public function mount(
        string $name = 'description',
        string $label = 'Description',
        ?string $value = null,
        int $rows = 8,
        string $placeholder = '',
        string $help = '',
        bool $required = false,
        ?string $id = null,
    ): void {
        $this->name = $name;
        $this->label = $label;
        $this->content = (string) ($value ?? '');
        $this->rows = $rows;
        $this->placeholder = $placeholder;
        $this->help = $help;
        $this->required = $required;
        $this->id = $id ?? str_replace(['[', ']'], ['-', ''], $name);
    }

    public function render(): View
    {
        return view('livewire.markdown.editor', [
            'previewHtml' => MarkdownRenderer::toSafeHtml($this->content),
        ]);
    }
}
