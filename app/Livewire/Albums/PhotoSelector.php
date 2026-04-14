<?php

namespace App\Livewire\Albums;

use Livewire\Component;

/**
 * AlbumPhotoSelector - Graphical cover photo selector component
 *
 * Renders a responsive grid of photo thumbnails with radio button selection.
 * Provides instant visual feedback via Livewire while maintaining form submission compatibility.
 */
class PhotoSelector extends Component
{
    /**
     * Form field name
     */
    public string $name = 'cover_photo_id';

    /**
     * Field label
     */
    public string $label = 'Cover photo';

    /**
     * Available photos to select from
     */
    public $photos = [];

    /**
     * Currently selected photo ID
     */
    public ?int $selected = null;

    /**
     * Helper text
     */
    public ?string $help = null;

    /**
     * Component ID for form accessibility
     */
    public string $componentId = '';

    /**
     * Mount the component with provided data
     */
    public function mount(
        ?string $name = null,
        ?string $label = null,
        $photos = null,
        ?int $selected = null,
        ?string $help = null,
    ): void {
        $this->name = $name ?? $this->name;
        $this->label = $label ?? $this->label;
        $this->photos = $photos ? collect($photos)->all() : [];
        $this->selected = $selected;
        $this->help = $help;
        $this->componentId = 'photo-selector-'.uniqid();
    }

    /**
     * Clear the selected cover photo
     */
    public function clearSelection(): void
    {
        $this->selected = null;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.albums.photo-selector');
    }
}
