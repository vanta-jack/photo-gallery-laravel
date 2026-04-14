<?php

namespace App\Livewire\Guestbook;

use App\Models\GuestbookEntry;
use App\Support\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Feed extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sort = 'latest';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->sort = 'latest';

        $this->resetPage();
    }

    public function render(): View
    {
        $entries = GuestbookEntry::query()
            ->with([
                'post' => fn ($query) => $query
                    ->withCount('votes')
                    ->with('user.profilePhoto:id,path'),
                'photo' => fn ($query) => $query->withCount(['ratings', 'comments']),
            ])
            ->when(
                $this->search !== '',
                function (Builder $query): void {
                    $term = '%'.addcslashes(trim($this->search), '%_\\').'%';

                    $query->whereHas('post', function (Builder $postQuery) use ($term): void {
                        $postQuery
                            ->where('title', 'like', $term)
                            ->orWhere('description', 'like', $term);
                    });
                },
            );

        if ($this->sort === 'oldest') {
            $entries->oldest();
        } else {
            $entries->latest();
        }

        $entries = $entries->paginate(20);

        $entries->getCollection()->transform(function (GuestbookEntry $entry): GuestbookEntry {
            $entry->post?->setAttribute('description_html', MarkdownRenderer::toSafeHtml($entry->post?->description));

            return $entry;
        });

        return view('livewire.guestbook.feed', compact('entries'));
    }
}
