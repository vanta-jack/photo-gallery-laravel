<?php

namespace App\Livewire\Home;

use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Milestone;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\Post;
use App\Support\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Feed extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = 'all';

    public string $sort = 'date_desc';

    protected function queryString(): array
    {
        return [
            'search' => ['except' => ''],
            'type' => ['except' => 'all'],
            'sort' => ['except' => 'date_desc'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
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
        $this->type = 'all';
        $this->sort = 'date_desc';

        $this->resetPage();
    }

    public function render(): View
    {
        $this->normalizeFilters();

        $items = collect()
            ->merge($this->buildPostItems())
            ->merge($this->buildAlbumItems())
            ->merge($this->buildMilestoneItems())
            ->merge($this->buildGuestbookItems());

        $sorted = match ($this->sort) {
            'date_asc' => $items->sortBy('created_at')->values(),
            'engagement_desc' => $items
                ->sortByDesc('created_at')
                ->sortByDesc('engagement_score')
                ->values(),
            default => $items->sortByDesc('created_at')->values(),
        };

        return view('livewire.home.feed', [
            'feedItems' => $this->paginate($sorted, 12),
            'filters' => [
                'search' => $this->search,
                'type' => $this->type,
                'sort' => $this->sort,
            ],
        ]);
    }

    private function buildPostItems(): Collection
    {
        if (! in_array($this->type, ['all', 'post'], true)) {
            return collect();
        }

        $query = Post::query()
            ->select(['id', 'user_id', 'photo_id', 'title', 'description', 'created_at'])
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
                'photo:id,path',
                'photos:id,path',
            ])
            ->withCount('votes')
            ->latest()
            ->limit(80);

        $this->applyTextSearch($query, ['title', 'description'], 'user');

        return $query
            ->get()
            ->map(function (Post $post): array {
                $mainPhoto = $post->photo ?? $post->photos->first();

                return [
                    'type' => 'post',
                    'id' => $post->id,
                    'title' => $post->title,
                    'description_html' => MarkdownRenderer::toSafeHtml($post->description),
                    'created_at' => $post->created_at,
                    'engagement_score' => (int) $post->votes_count,
                    'engagement_label' => sprintf('%d votes', (int) $post->votes_count),
                    'author' => $this->displayName($post->user?->first_name, $post->user?->last_name),
                    'author_user' => $post->user,
                    'url' => route('posts.show', $post),
                    'icon' => 'pen',
                    'image_url' => $this->resolveImageUrl($mainPhoto?->path),
                ];
            });
    }

    private function buildAlbumItems(): Collection
    {
        if (! in_array($this->type, ['all', 'album'], true)) {
            return collect();
        }

        $query = Album::query()
            ->select(['id', 'user_id', 'cover_photo_id', 'title', 'description', 'created_at'])
            ->where('is_private', false)
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
                'coverPhoto:id,path',
            ])
            ->withCount('photos')
            ->addSelect([
                'comments_count' => PhotoComment::query()
                    ->selectRaw('COUNT(*)')
                    ->join('album_photo', 'album_photo.photo_id', '=', 'photo_comments.photo_id')
                    ->whereColumn('album_photo.album_id', 'albums.id'),
                'ratings_count' => PhotoRating::query()
                    ->selectRaw('COUNT(*)')
                    ->join('album_photo', 'album_photo.photo_id', '=', 'photo_ratings.photo_id')
                    ->whereColumn('album_photo.album_id', 'albums.id'),
            ])
            ->latest()
            ->limit(80);

        $this->applyTextSearch($query, ['title', 'description'], 'user');

        return $query
            ->get()
            ->map(function (Album $album): array {
                $engagementScore = (int) $album->comments_count + (int) $album->ratings_count;

                return [
                    'type' => 'album',
                    'id' => $album->id,
                    'title' => $album->title,
                    'description_html' => MarkdownRenderer::toSafeHtml($album->description),
                    'created_at' => $album->created_at,
                    'engagement_score' => $engagementScore,
                    'engagement_label' => sprintf('%d comments + ratings', $engagementScore),
                    'author' => $this->displayName($album->user?->first_name, $album->user?->last_name),
                    'author_user' => $album->user,
                    'url' => route('albums.show', $album),
                    'icon' => 'folder',
                    'image_url' => $album->coverPhoto ? Storage::url($album->coverPhoto->path) : null,
                ];
            });
    }

    private function buildMilestoneItems(): Collection
    {
        if (! in_array($this->type, ['all', 'milestone'], true)) {
            return collect();
        }

        $query = Milestone::query()
            ->select(['id', 'user_id', 'photo_id', 'label', 'description', 'created_at'])
            ->where('is_public', true)
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
                'photo:id,path',
            ])
            ->addSelect([
                'comments_count' => PhotoComment::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('photo_comments.photo_id', 'milestones.photo_id'),
                'ratings_count' => PhotoRating::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('photo_ratings.photo_id', 'milestones.photo_id'),
            ])
            ->latest()
            ->limit(80);

        $this->applyTextSearch($query, ['label', 'description'], 'user');

        return $query
            ->get()
            ->map(function (Milestone $milestone): array {
                $engagementScore = (int) $milestone->comments_count + (int) $milestone->ratings_count;

                return [
                    'type' => 'milestone',
                    'id' => $milestone->id,
                    'title' => $milestone->label,
                    'description_html' => MarkdownRenderer::toSafeHtml($milestone->description),
                    'created_at' => $milestone->created_at,
                    'engagement_score' => $engagementScore,
                    'engagement_label' => sprintf('%d comments + ratings', $engagementScore),
                    'author' => $this->displayName($milestone->user?->first_name, $milestone->user?->last_name),
                    'author_user' => $milestone->user,
                    'url' => null,
                    'icon' => 'target',
                    'image_url' => $milestone->photo ? Storage::url($milestone->photo->path) : null,
                ];
            });
    }

    private function buildGuestbookItems(): Collection
    {
        if (! in_array($this->type, ['all', 'guestbook'], true)) {
            return collect();
        }

        $query = GuestbookEntry::query()
            ->select(['id', 'post_id', 'photo_id', 'created_at'])
            ->with([
                'post' => fn ($postQuery) => $postQuery
                    ->select(['id', 'user_id', 'title', 'description'])
                    ->with([
                        'user:id,first_name,last_name,profile_photo_id',
                        'user.profilePhoto:id,path',
                    ])
                    ->withCount('votes'),
                'photo:id,path',
            ])
            ->addSelect([
                'comments_count' => PhotoComment::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('photo_comments.photo_id', 'guestbook_entries.photo_id'),
                'ratings_count' => PhotoRating::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('photo_ratings.photo_id', 'guestbook_entries.photo_id'),
            ])
            ->latest()
            ->limit(80);

        $search = $this->sanitizedSearchTerm();
        if ($search !== '') {
            $query->whereHas('post', function (Builder $postQuery) use ($search): void {
                $postQuery
                    ->where(function (Builder $textQuery) use ($search): void {
                        $textQuery
                            ->where('title', 'like', $search)
                            ->orWhere('description', 'like', $search);
                    })
                    ->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                        $userQuery
                            ->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search);
                    });
            });
        }

        return $query
            ->get()
            ->map(function (GuestbookEntry $entry): array {
                $voteCount = (int) ($entry->post?->votes_count ?? 0);
                $engagementScore = $voteCount + (int) $entry->comments_count + (int) $entry->ratings_count;

                return [
                    'type' => 'guestbook',
                    'id' => $entry->id,
                    'title' => $entry->post?->title ?? 'Guestbook entry',
                    'description_html' => MarkdownRenderer::toSafeHtml($entry->post?->description),
                    'created_at' => $entry->created_at,
                    'engagement_score' => $engagementScore,
                    'engagement_label' => sprintf('%d votes + comments + ratings', $engagementScore),
                    'author' => $this->displayName($entry->post?->user?->first_name, $entry->post?->user?->last_name),
                    'author_user' => $entry->post?->user,
                    'url' => route('guestbook.index'),
                    'icon' => 'pen-tool',
                    'image_url' => $entry->photo ? Storage::url($entry->photo->path) : null,
                ];
            });
    }

    private function applyTextSearch(Builder $query, array $columns, string $authorRelation): void
    {
        $search = $this->sanitizedSearchTerm();
        if ($search === '') {
            return;
        }

        $query->where(function (Builder $baseQuery) use ($columns, $authorRelation, $search): void {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $baseQuery->where($column, 'like', $search);
                } else {
                    $baseQuery->orWhere($column, 'like', $search);
                }
            }

            $baseQuery->orWhereHas($authorRelation, function (Builder $authorQuery) use ($search): void {
                $authorQuery
                    ->where('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search);
            });
        });
    }

    private function displayName(?string $firstName, ?string $lastName): string
    {
        $displayName = trim(sprintf('%s %s', (string) $firstName, (string) $lastName));

        return $displayName !== '' ? $displayName : 'Guest';
    }

    private function paginate(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = $this->getPage();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => route('home'),
                'query' => array_filter([
                    'search' => $this->search,
                    'type' => $this->type !== 'all' ? $this->type : null,
                    'sort' => $this->sort !== 'date_desc' ? $this->sort : null,
                ]),
            ],
        );
    }

    private function normalizeFilters(): void
    {
        $this->type = in_array($this->type, ['all', 'post', 'album', 'milestone', 'guestbook'], true)
            ? $this->type
            : 'all';

        $this->sort = in_array($this->sort, ['date_desc', 'date_asc', 'engagement_desc'], true)
            ? $this->sort
            : 'date_desc';

        $this->search = trim($this->search);
    }

    private function sanitizedSearchTerm(): string
    {
        if ($this->search === '') {
            return '';
        }

        return '%'.addcslashes($this->search, '%_\\').'%';
    }

    private function resolveImageUrl(?string $path): ?string
    {
        $resolvedPath = trim((string) $path);
        if ($resolvedPath === '') {
            return null;
        }

        if (
            str_starts_with($resolvedPath, 'http://')
            || str_starts_with($resolvedPath, 'https://')
            || str_starts_with($resolvedPath, '/')
        ) {
            return $resolvedPath;
        }

        return Storage::url($resolvedPath);
    }
}
