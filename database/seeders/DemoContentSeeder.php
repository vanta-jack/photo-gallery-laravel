<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\GuestbookEntry;
use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DemoContentSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const DEMO_USER_EMAILS = [
        'user@domain.com',
        'admin@domain.com',
    ];

    /**
     * @var array<int, array{
     *     key: string,
     *     owner: string,
     *     path: string,
     *     title: string,
     *     description: ?string,
     *     created_at: string,
     *     palette: array{0: string, 1: string}
     * }>
     */
    private const PHOTOS = [
        [
            'key' => 'user-coastline',
            'owner' => 'user@domain.com',
            'path' => 'photos/demo/user/coastline-sunrise.svg',
            'title' => 'Coastline Sunrise',
            'description' => 'Golden hour colors captured during a family beach walk.',
            'created_at' => '2026-02-01 08:15:00',
            'palette' => ['#1d4ed8', '#38bdf8'],
        ],
        [
            'key' => 'user-forest-trail',
            'owner' => 'user@domain.com',
            'path' => 'photos/demo/user/forest-trail.svg',
            'title' => 'Forest Trail',
            'description' => null,
            'created_at' => '2026-02-02 09:20:00',
            'palette' => ['#166534', '#4ade80'],
        ],
        [
            'key' => 'user-studio-portrait',
            'owner' => 'user@domain.com',
            'path' => 'photos/demo/user/studio-portrait.svg',
            'title' => 'Studio Portrait',
            'description' => 'Sharp portrait used for profile and milestone storytelling.',
            'created_at' => '2026-02-03 10:30:00',
            'palette' => ['#7c2d12', '#fb923c'],
        ],
        [
            'key' => 'admin-city-night',
            'owner' => 'admin@domain.com',
            'path' => 'photos/demo/admin/city-night.svg',
            'title' => 'City at Night',
            'description' => 'Night skyline used by moderation examples and guestbook highlights.',
            'created_at' => '2026-02-04 18:05:00',
            'palette' => ['#4c1d95', '#a855f7'],
        ],
        [
            'key' => 'admin-doc-board',
            'owner' => 'admin@domain.com',
            'path' => 'photos/demo/admin/moderation-board.svg',
            'title' => 'Moderation Board Snapshot',
            'description' => 'Reference image for moderation workflow previews.',
            'created_at' => '2026-02-05 14:10:00',
            'palette' => ['#0f766e', '#22d3ee'],
        ],
        [
            'key' => 'admin-minimal-draft',
            'owner' => 'admin@domain.com',
            'path' => 'photos/demo/admin/minimal-draft.svg',
            'title' => 'Minimal Draft',
            'description' => null,
            'created_at' => '2026-02-06 07:45:00',
            'palette' => ['#111827', '#6b7280'],
        ],
    ];

    /**
     * @var array<int, array{
     *     owner: string,
     *     title: string,
     *     description: ?string,
     *     is_private: bool,
     *     is_favorite: bool,
     *     cover_photo_key: ?string,
     *     photo_keys: array<int, string>,
     *     created_at: string
     * }>
     */
    private const ALBUMS = [
        [
            'owner' => 'user@domain.com',
            'title' => 'Family Highlights',
            'description' => 'Curated moments that are safe to share publicly.',
            'is_private' => false,
            'is_favorite' => true,
            'cover_photo_key' => 'user-coastline',
            'photo_keys' => ['user-coastline', 'user-forest-trail', 'user-studio-portrait'],
            'created_at' => '2026-02-10 10:00:00',
        ],
        [
            'owner' => 'user@domain.com',
            'title' => 'Private Draft Shots',
            'description' => 'Work-in-progress edits kept private for now.',
            'is_private' => true,
            'is_favorite' => false,
            'cover_photo_key' => 'user-forest-trail',
            'photo_keys' => ['user-forest-trail'],
            'created_at' => '2026-02-11 10:00:00',
        ],
        [
            'owner' => 'user@domain.com',
            'title' => 'Upcoming Uploads',
            'description' => 'Reserved album showing an intentional empty state.',
            'is_private' => false,
            'is_favorite' => false,
            'cover_photo_key' => null,
            'photo_keys' => [],
            'created_at' => '2026-02-12 10:00:00',
        ],
        [
            'owner' => 'admin@domain.com',
            'title' => 'Moderation Showcase',
            'description' => 'Public examples used during moderation demos.',
            'is_private' => false,
            'is_favorite' => false,
            'cover_photo_key' => 'admin-city-night',
            'photo_keys' => ['admin-city-night', 'admin-doc-board'],
            'created_at' => '2026-02-13 10:00:00',
        ],
        [
            'owner' => 'admin@domain.com',
            'title' => 'Admin Review Queue',
            'description' => null,
            'is_private' => true,
            'is_favorite' => false,
            'cover_photo_key' => null,
            'photo_keys' => [],
            'created_at' => '2026-02-14 10:00:00',
        ],
    ];

    /**
     * @var array<int, array{
     *     key: string,
     *     owner: ?string,
     *     title: string,
     *     description: string,
     *     created_at: string
     * }>
     */
    private const POSTS = [
        [
            'key' => 'user-travel-log',
            'owner' => 'user@domain.com',
            'title' => 'Travel Log: Day 1',
            'description' => "## Day one highlights\n\n- Arrived early for sunrise.\n- Mapped a short family trail.\n\nCaptured enough scenes for both public and private albums.",
            'created_at' => '2026-02-20 09:00:00',
        ],
        [
            'key' => 'admin-welcome-note',
            'owner' => 'admin@domain.com',
            'title' => "Moderator's Welcome Note",
            'description' => "Welcome to the demo space.\n\nPlease keep uploads respectful and report anything that needs moderation review.",
            'created_at' => '2026-02-21 09:00:00',
        ],
        [
            'key' => 'user-quick-check-in',
            'owner' => 'user@domain.com',
            'title' => 'Quick Check-in',
            'description' => 'Short update while preparing the next batch of uploads.',
            'created_at' => '2026-02-22 09:00:00',
        ],
        [
            'key' => 'guestbook-signed',
            'owner' => 'user@domain.com',
            'title' => 'Guestbook · Family thanks',
            'description' => 'Thanks for visiting the gallery. More stories are on the way.',
            'created_at' => '2026-02-23 09:00:00',
        ],
        [
            'key' => 'guestbook-anonymous',
            'owner' => null,
            'title' => 'Guestbook · Anonymous hello',
            'description' => 'Great gallery and easy navigation.',
            'created_at' => '2026-02-24 09:00:00',
        ],
        [
            'key' => 'guestbook-admin-followup',
            'owner' => 'admin@domain.com',
            'title' => 'Guestbook · Moderator follow-up',
            'description' => 'Appreciate the feedback. Moderation queue has been reviewed.',
            'created_at' => '2026-02-25 09:00:00',
        ],
    ];

    /**
     * @var array<int, array{post_key: string, photo_key: ?string, created_at: string}>
     */
    private const GUESTBOOK_ENTRIES = [
        [
            'post_key' => 'guestbook-signed',
            'photo_key' => 'user-coastline',
            'created_at' => '2026-03-01 12:00:00',
        ],
        [
            'post_key' => 'guestbook-anonymous',
            'photo_key' => null,
            'created_at' => '2026-03-02 12:00:00',
        ],
        [
            'post_key' => 'guestbook-admin-followup',
            'photo_key' => 'admin-city-night',
            'created_at' => '2026-03-03 12:00:00',
        ],
    ];

    /**
     * @var array<int, array{user: string, photo_key: string, rating: int, created_at: string}>
     */
    private const PHOTO_RATINGS = [
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'user-coastline',
            'rating' => 5,
            'created_at' => '2026-03-05 11:00:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'user-coastline',
            'rating' => 4,
            'created_at' => '2026-03-05 11:05:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'admin-city-night',
            'rating' => 5,
            'created_at' => '2026-03-06 11:00:00',
        ],
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'admin-city-night',
            'rating' => 4,
            'created_at' => '2026-03-06 11:05:00',
        ],
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'user-studio-portrait',
            'rating' => 3,
            'created_at' => '2026-03-07 11:00:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'admin-doc-board',
            'rating' => 2,
            'created_at' => '2026-03-07 11:05:00',
        ],
    ];

    /**
     * @var array<int, array{user: string, photo_key: string, body: string, created_at: string}>
     */
    private const PHOTO_COMMENTS = [
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'user-coastline',
            'body' => 'Great sunrise framing for the public feed.',
            'created_at' => '2026-03-08 13:00:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'user-coastline',
            'body' => 'Captured right before breakfast at the shore.',
            'created_at' => '2026-03-08 13:05:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'admin-city-night',
            'body' => 'Neon reflections look sharp.',
            'created_at' => '2026-03-09 13:00:00',
        ],
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'admin-city-night',
            'body' => 'Keeping this as a moderation header candidate.',
            'created_at' => '2026-03-09 13:05:00',
        ],
        [
            'user' => 'admin@domain.com',
            'photo_key' => 'user-studio-portrait',
            'body' => 'Good profile-photo candidate.',
            'created_at' => '2026-03-10 13:00:00',
        ],
        [
            'user' => 'user@domain.com',
            'photo_key' => 'admin-doc-board',
            'body' => 'Helpful context for moderation workflows.',
            'created_at' => '2026-03-10 13:05:00',
        ],
    ];

    /**
     * @var array<int, array{user: string, post_key: string, created_at: string}>
     */
    private const POST_VOTES = [
        [
            'user' => 'admin@domain.com',
            'post_key' => 'user-travel-log',
            'created_at' => '2026-03-11 15:00:00',
        ],
        [
            'user' => 'user@domain.com',
            'post_key' => 'user-travel-log',
            'created_at' => '2026-03-11 15:05:00',
        ],
        [
            'user' => 'user@domain.com',
            'post_key' => 'admin-welcome-note',
            'created_at' => '2026-03-12 15:00:00',
        ],
        [
            'user' => 'admin@domain.com',
            'post_key' => 'guestbook-signed',
            'created_at' => '2026-03-12 15:05:00',
        ],
        [
            'user' => 'user@domain.com',
            'post_key' => 'guestbook-signed',
            'created_at' => '2026-03-12 15:10:00',
        ],
        [
            'user' => 'user@domain.com',
            'post_key' => 'guestbook-admin-followup',
            'created_at' => '2026-03-13 15:00:00',
        ],
        [
            'user' => 'admin@domain.com',
            'post_key' => 'guestbook-admin-followup',
            'created_at' => '2026-03-13 15:05:00',
        ],
    ];

    public function run(): void
    {
        $users = User::query()
            ->whereIn('email', self::DEMO_USER_EMAILS)
            ->get()
            ->keyBy('email');

        if ($users->count() !== count(self::DEMO_USER_EMAILS)) {
            return;
        }

        $photos = $this->seedPhotos($users);
        $this->seedAlbums($users, $photos);
        $posts = $this->seedPosts($users);
        $this->seedGuestbookEntries($posts, $photos);
        $this->seedPhotoRatings($users, $photos);
        $this->seedPhotoComments($users, $photos);
        $this->seedPostVotes($users, $posts);
    }

    /**
     * @param  Collection<string, User>  $users
     * @return Collection<string, Photo>
     */
    private function seedPhotos(Collection $users): Collection
    {
        return collect(self::PHOTOS)->mapWithKeys(function (array $photoDefinition) use ($users): array {
            $owner = $this->userFromEmail($users, $photoDefinition['owner']);

            $this->storeDemoPhotoAsset(
                $photoDefinition['path'],
                $photoDefinition['title'],
                $photoDefinition['palette'],
            );

            $photo = Photo::query()->updateOrCreate(
                ['path' => $photoDefinition['path']],
                [
                    'user_id' => $owner->id,
                    'title' => $photoDefinition['title'],
                    'description' => $photoDefinition['description'],
                ],
            );

            $this->setModelTimestamps($photo, $photoDefinition['created_at']);

            return [$photoDefinition['key'] => $photo];
        });
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Photo>  $photos
     */
    private function seedAlbums(Collection $users, Collection $photos): void
    {
        foreach (self::ALBUMS as $albumDefinition) {
            $owner = $this->userFromEmail($users, $albumDefinition['owner']);
            $photoIds = collect($albumDefinition['photo_keys'])
                ->map(fn (string $photoKey): int => $this->photoFromKey($photos, $photoKey)->id)
                ->all();

            $coverPhotoId = $albumDefinition['cover_photo_key'] !== null
                ? $this->photoFromKey($photos, $albumDefinition['cover_photo_key'])->id
                : null;

            if ($coverPhotoId !== null && ! in_array($coverPhotoId, $photoIds, true)) {
                $coverPhotoId = null;
            }

            $album = Album::query()->updateOrCreate(
                [
                    'user_id' => $owner->id,
                    'title' => $albumDefinition['title'],
                ],
                [
                    'description' => $albumDefinition['description'],
                    'is_private' => $albumDefinition['is_private'],
                    'is_favorite' => $albumDefinition['is_favorite'],
                    'cover_photo_id' => $coverPhotoId,
                ],
            );

            $album->photos()->sync($photoIds);
            $this->setModelTimestamps($album, $albumDefinition['created_at']);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @return Collection<string, Post>
     */
    private function seedPosts(Collection $users): Collection
    {
        return collect(self::POSTS)->mapWithKeys(function (array $postDefinition) use ($users): array {
            $ownerId = $postDefinition['owner'] !== null
                ? $this->userFromEmail($users, $postDefinition['owner'])->id
                : null;

            $post = Post::query()->updateOrCreate(
                ['title' => $postDefinition['title']],
                [
                    'user_id' => $ownerId,
                    'description' => $postDefinition['description'],
                ],
            );

            $this->setModelTimestamps($post, $postDefinition['created_at']);

            return [$postDefinition['key'] => $post];
        });
    }

    /**
     * @param  Collection<string, Post>  $posts
     * @param  Collection<string, Photo>  $photos
     */
    private function seedGuestbookEntries(Collection $posts, Collection $photos): void
    {
        foreach (self::GUESTBOOK_ENTRIES as $entryDefinition) {
            $post = $this->postFromKey($posts, $entryDefinition['post_key']);

            $photoId = $entryDefinition['photo_key'] !== null
                ? $this->photoFromKey($photos, $entryDefinition['photo_key'])->id
                : null;

            $entry = GuestbookEntry::query()->updateOrCreate(
                ['post_id' => $post->id],
                ['photo_id' => $photoId],
            );

            $this->setModelTimestamps($entry, $entryDefinition['created_at']);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Photo>  $photos
     */
    private function seedPhotoRatings(Collection $users, Collection $photos): void
    {
        foreach (self::PHOTO_RATINGS as $ratingDefinition) {
            $user = $this->userFromEmail($users, $ratingDefinition['user']);
            $photo = $this->photoFromKey($photos, $ratingDefinition['photo_key']);

            $rating = PhotoRating::query()->updateOrCreate(
                [
                    'photo_id' => $photo->id,
                    'user_id' => $user->id,
                ],
                ['rating' => $ratingDefinition['rating']],
            );

            $this->setModelTimestamps($rating, $ratingDefinition['created_at']);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Photo>  $photos
     */
    private function seedPhotoComments(Collection $users, Collection $photos): void
    {
        foreach (self::PHOTO_COMMENTS as $commentDefinition) {
            $user = $this->userFromEmail($users, $commentDefinition['user']);
            $photo = $this->photoFromKey($photos, $commentDefinition['photo_key']);

            $comment = PhotoComment::query()->updateOrCreate(
                [
                    'photo_id' => $photo->id,
                    'user_id' => $user->id,
                    'body' => $commentDefinition['body'],
                ],
                [],
            );

            $this->setModelTimestamps($comment, $commentDefinition['created_at']);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Post>  $posts
     */
    private function seedPostVotes(Collection $users, Collection $posts): void
    {
        foreach (self::POST_VOTES as $voteDefinition) {
            $user = $this->userFromEmail($users, $voteDefinition['user']);
            $post = $this->postFromKey($posts, $voteDefinition['post_key']);

            $vote = PostVote::query()->updateOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ],
                [],
            );

            $this->setModelTimestamps($vote, $voteDefinition['created_at']);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     */
    private function userFromEmail(Collection $users, string $email): User
    {
        $user = $users->get($email);

        if (! $user instanceof User) {
            throw new \RuntimeException("Missing demo user: {$email}");
        }

        return $user;
    }

    /**
     * @param  Collection<string, Photo>  $photos
     */
    private function photoFromKey(Collection $photos, string $photoKey): Photo
    {
        $photo = $photos->get($photoKey);

        if (! $photo instanceof Photo) {
            throw new \RuntimeException("Missing demo photo: {$photoKey}");
        }

        return $photo;
    }

    /**
     * @param  Collection<string, Post>  $posts
     */
    private function postFromKey(Collection $posts, string $postKey): Post
    {
        $post = $posts->get($postKey);

        if (! $post instanceof Post) {
            throw new \RuntimeException("Missing demo post: {$postKey}");
        }

        return $post;
    }

    private function setModelTimestamps(Model $model, string $timestamp): void
    {
        $date = CarbonImmutable::parse($timestamp);

        $model->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
        ])->saveQuietly();
    }

    /**
     * @param  array{0: string, 1: string}  $palette
     */
    private function storeDemoPhotoAsset(string $path, string $title, array $palette): void
    {
        [$startColor, $endColor] = $palette;
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800" role="img" aria-label="{$safeTitle}">
    <defs>
        <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="{$startColor}" />
            <stop offset="100%" stop-color="{$endColor}" />
        </linearGradient>
    </defs>
    <rect width="1200" height="800" fill="url(#bg)" />
    <rect x="70" y="70" width="1060" height="660" rx="26" ry="26" fill="rgba(15,23,42,0.20)" />
    <text x="600" y="410" font-size="64" text-anchor="middle" fill="#ffffff" font-family="Arial, Helvetica, sans-serif">{$safeTitle}</text>
    <text x="600" y="470" font-size="30" text-anchor="middle" fill="#e2e8f0" font-family="Arial, Helvetica, sans-serif">Demo seed asset</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);
    }
}
