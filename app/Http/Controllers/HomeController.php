<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Milestone;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $sort = $request->string('sort')->toString();

        $normalizedType = in_array($type, ['all', 'post', 'album', 'milestone', 'guestbook'], true) ? $type : 'all';
        $normalizedSort = in_array($sort, ['date_desc', 'date_asc', 'engagement_desc'], true) ? $sort : 'date_desc';

        $items = collect()
            ->merge($this->buildPostItems($normalizedType))
            ->merge($this->buildAlbumItems($normalizedType))
            ->merge($this->buildMilestoneItems($normalizedType))
            ->merge($this->buildGuestbookItems($normalizedType));

        $sorted = match ($normalizedSort) {
            'date_asc' => $items->sortBy('created_at')->values(),
            'engagement_desc' => $items
                ->sortByDesc('created_at')
                ->sortByDesc('engagement_score')
                ->values(),
            default => $items->sortByDesc('created_at')->values(),
        };

        return view('dashboard', [
            'feedItems' => $this->paginate($sorted, 12, $request),
            'filters' => [
                'type' => $normalizedType,
                'sort' => $normalizedSort,
            ],
        ]);
    }

    private function buildPostItems(string $type): Collection
    {
        if (! in_array($type, ['all', 'post'], true)) {
            return collect();
        }

        return Post::query()
            ->select(['id', 'user_id', 'title', 'description', 'created_at'])
            ->with([
                'user:id,first_name,last_name,profile_photo_id',
                'user.profilePhoto:id,path',
            ])
            ->withCount('votes')
            ->latest()
            ->limit(80)
            ->get()
            ->map(function (Post $post): array {
                return [
                    'type' => 'post',
                    'id' => $post->id,
                    'title' => $post->title,
                    'description_html' => $this->renderMarkdown($post->description),
                    'created_at' => $post->created_at,
                    'engagement_score' => (int) $post->votes_count,
                    'engagement_label' => sprintf('%d votes', (int) $post->votes_count),
                    'author' => $this->displayName($post->user?->first_name, $post->user?->last_name),
                    'author_user' => $post->user,
                    'url' => route('posts.show', $post),
                    'icon' => 'pen',
                    'image_url' => null,
                ];
            });
    }

    private function buildAlbumItems(string $type): Collection
    {
        if (! in_array($type, ['all', 'album'], true)) {
            return collect();
        }

        return Album::query()
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
            ->limit(80)
            ->get()
            ->map(function (Album $album): array {
                $engagementScore = (int) $album->comments_count + (int) $album->ratings_count;

                return [
                    'type' => 'album',
                    'id' => $album->id,
                    'title' => $album->title,
                    'description_html' => $this->renderMarkdown($album->description),
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

    private function buildMilestoneItems(string $type): Collection
    {
        if (! in_array($type, ['all', 'milestone'], true)) {
            return collect();
        }

        return Milestone::query()
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
            ->limit(80)
            ->get()
            ->map(function (Milestone $milestone): array {
                $engagementScore = (int) $milestone->comments_count + (int) $milestone->ratings_count;

                return [
                    'type' => 'milestone',
                    'id' => $milestone->id,
                    'title' => $milestone->label,
                    'description_html' => $this->renderMarkdown($milestone->description),
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

    private function buildGuestbookItems(string $type): Collection
    {
        if (! in_array($type, ['all', 'guestbook'], true)) {
            return collect();
        }

        return GuestbookEntry::query()
            ->select(['id', 'post_id', 'photo_id', 'created_at'])
            ->with([
                'post' => fn ($query) => $query
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
            ->limit(80)
            ->get()
            ->map(function (GuestbookEntry $entry): array {
                $voteCount = (int) ($entry->post?->votes_count ?? 0);
                $engagementScore = $voteCount + (int) $entry->comments_count + (int) $entry->ratings_count;

                return [
                    'type' => 'guestbook',
                    'id' => $entry->id,
                    'title' => $entry->post?->title ?? 'Guestbook entry',
                    'description_html' => $this->renderMarkdown($entry->post?->description),
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

    private function renderMarkdown(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    private function displayName(?string $firstName, ?string $lastName): string
    {
        $displayName = trim(sprintf('%s %s', (string) $firstName, (string) $lastName));

        return $displayName !== '' ? $displayName : 'Guest';
    }

    private function paginate(Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );
    }
}
