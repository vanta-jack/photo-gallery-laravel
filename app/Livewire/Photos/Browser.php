<?php

namespace App\Livewire\Photos;

use App\Models\Photo;
use App\Support\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Browser extends Component
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

    public function render(): View
    {
        $user = auth()->user();
        if ($user === null) {
            abort(403);
        }

        $photos = Photo::query()
            ->whereBelongsTo($user)
            ->with(['user:id,first_name,last_name,profile_photo_id', 'user.profilePhoto:id,path'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->when(
                $this->search !== '',
                function (Builder $query): void {
                    $term = '%'.addcslashes(trim($this->search), '%_\\').'%';

                    $query->where(function (Builder $searchQuery) use ($term): void {
                        $searchQuery
                            ->where('title', 'like', $term)
                            ->orWhere('description', 'like', $term);
                    });
                },
            );

        $sortedQuery = match ($this->sort) {
            'oldest' => $photos->oldest(),
            'rating_desc' => $photos->orderByDesc('ratings_avg_rating')->latest(),
            'rating_asc' => $photos->orderBy('ratings_avg_rating')->latest(),
            default => $photos->latest(),
        };

        $photos = $sortedQuery->paginate(12);

        $photos->getCollection()->transform(function (Photo $photo): Photo {
            $photo->setAttribute('description_html', MarkdownRenderer::toSafeHtml($photo->description));

            return $photo;
        });

        return view('livewire.photos.browser', [
            'photos' => $photos,
        ]);
    }
}
