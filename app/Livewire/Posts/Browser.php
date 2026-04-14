<?php

namespace App\Livewire\Posts;

use App\Models\Post;
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

        $posts = Post::query()
            ->whereBelongsTo($user)
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
            ])
            ->withCount('votes')
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
            'oldest' => $posts->oldest(),
            'votes_desc' => $posts->orderByDesc('votes_count')->latest(),
            default => $posts->latest(),
        };

        $posts = $sortedQuery->paginate(12);

        $posts->getCollection()->transform(function (Post $post): Post {
            $post->setAttribute('description_html', MarkdownRenderer::toSafeHtml($post->description));

            return $post;
        });

        return view('livewire.posts.browser', [
            'posts' => $posts,
        ]);
    }
}
