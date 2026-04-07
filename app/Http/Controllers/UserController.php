<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\PhotoComment;
use App\Models\PhotoRating;
use App\Models\PostVote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * UserController
 *
 * Manages user profile updates and public profile display.
 */
class UserController extends Controller
{
    /**
     * Display the authenticated user's profile in read-only mode.
     */
    public function profile(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return $this->renderProfile($user, true);
    }

    /**
     * Show profile edit form
     *
     * Defaults to editing current user's profile
     */
    public function edit(?User $user = null): View
    {
        // If no user specified, edit current user
        $user = $user ?? auth()->user();

        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(UpdateUserRequest $request, ?User $user = null): RedirectResponse
    {
        // If no user specified, update current user
        $user = $user ?? auth()->user();

        $this->authorize('update', $user);

        $user->update($request->validated());

        return redirect()
            ->route('profile.show')
            ->with('status', 'Profile updated successfully!');
    }

    /**
     * Display the public profile page.
     */
    public function show(User $user): View
    {
        return $this->renderProfile($user);
    }

    private function renderProfile(User $user, bool $showEditCta = false): View
    {
        $user->load('profilePhoto:id,path,title');

        $displayName = trim(sprintf('%s %s', (string) $user->first_name, (string) $user->last_name));
        if ($displayName === '') {
            $displayName = sprintf('User #%d', $user->id);
        }

        $academicHistory = $this->normalizeAcademicHistory($user->academic_history);
        $professionalExperience = $this->normalizeProfessionalExperience($user->professional_experience);
        $certifications = $this->normalizeCertifications($user->certifications);
        $skills = $this->normalizeSkills($user->skills);
        $otherLinks = $this->normalizeOtherLinks($user->other_links);

        $linkedin = $this->normalizeUrl($user->linkedin);
        $github = $this->normalizeUrl($user->github);
        $phone = $user->phone_public ? trim((string) $user->phone) : null;

        if ($phone === '') {
            $phone = null;
        }

        $bioHtml = null;
        if (filled($user->bio)) {
            $bioHtml = Str::markdown($user->bio, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
        }

        $engagement = $this->buildEngagementMetrics($user);

        return view('users.show', [
            'user' => $user,
            'displayName' => $displayName,
            'bioHtml' => $bioHtml,
            'academicHistory' => $academicHistory,
            'professionalExperience' => $professionalExperience,
            'certifications' => $certifications,
            'skills' => $skills,
            'otherLinks' => $otherLinks,
            'contact' => [
                'linkedin' => $linkedin,
                'github' => $github,
                'phone' => $phone,
            ],
            'engagement' => $engagement,
            'showEditCta' => $showEditCta,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $entries
     * @return array<int, array<string, string>>
     */
    private function normalizeAcademicHistory(?array $entries): array
    {
        return collect($entries ?? [])
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['degree'] ?? null) && filled($entry['institution'] ?? null))
            ->map(fn ($entry): array => [
                'degree' => trim((string) ($entry['degree'] ?? '')),
                'institution' => trim((string) ($entry['institution'] ?? '')),
                'graduation_date' => trim((string) ($entry['graduation_date'] ?? '')),
            ])
            ->sortBy('graduation_date')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $entries
     * @return array<int, array<string, string|null>>
     */
    private function normalizeProfessionalExperience(?array $entries): array
    {
        return collect($entries ?? [])
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['title'] ?? null) && filled($entry['company'] ?? null))
            ->map(fn ($entry): array => [
                'title' => trim((string) ($entry['title'] ?? '')),
                'company' => trim((string) ($entry['company'] ?? '')),
                'start_date' => trim((string) ($entry['start_date'] ?? '')),
                'end_date' => filled($entry['end_date'] ?? null) ? trim((string) $entry['end_date']) : null,
                'description' => filled($entry['description'] ?? null) ? trim((string) $entry['description']) : null,
            ])
            ->sortByDesc('start_date')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $entries
     * @return array<int, array<string, string|null>>
     */
    private function normalizeCertifications(?array $entries): array
    {
        return collect($entries ?? [])
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['name'] ?? null))
            ->map(fn ($entry): array => [
                'name' => trim((string) ($entry['name'] ?? '')),
                'issuer' => filled($entry['issuer'] ?? null) ? trim((string) $entry['issuer']) : null,
                'awarded_on' => filled($entry['awarded_on'] ?? null) ? trim((string) $entry['awarded_on']) : null,
            ])
            ->sortByDesc('awarded_on')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>|null  $entries
     * @return array<int, string>
     */
    private function normalizeSkills(?array $entries): array
    {
        return collect($entries ?? [])
            ->filter(fn ($entry): bool => is_string($entry) && filled($entry))
            ->map(fn ($entry): string => trim($entry))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $entries
     * @return array<int, array{label: string, url: string}>
     */
    private function normalizeOtherLinks(?array $entries): array
    {
        return collect($entries ?? [])
            ->filter(fn ($entry): bool => is_array($entry) && filled($entry['label'] ?? null) && filled($entry['url'] ?? null))
            ->map(function ($entry): ?array {
                $url = $this->normalizeUrl((string) $entry['url']);
                if ($url === null) {
                    return null;
                }

                return [
                    'label' => trim((string) $entry['label']),
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEngagementMetrics(User $user): array
    {
        $totalPostVotes = PostVote::query()
            ->join('posts', 'posts.id', '=', 'post_votes.post_id')
            ->where('posts.user_id', $user->id)
            ->count();

        $totalPhotoComments = PhotoComment::query()
            ->join('photos', 'photos.id', '=', 'photo_comments.photo_id')
            ->where('photos.user_id', $user->id)
            ->count();

        $totalPhotoRatings = PhotoRating::query()
            ->join('photos', 'photos.id', '=', 'photo_ratings.photo_id')
            ->where('photos.user_id', $user->id)
            ->count();

        $topVotedPost = $user->posts()
            ->select(['posts.id', 'posts.title'])
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->orderByDesc('posts.id')
            ->first();

        $topRatedPhoto = $user->photos()
            ->select(['photos.id', 'photos.title', 'photos.path'])
            ->withCount(['ratings', 'comments'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_count')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('photos.id')
            ->first();

        return [
            'summary' => [
                'post_votes' => $totalPostVotes,
                'photo_comments' => $totalPhotoComments,
                'photo_ratings' => $totalPhotoRatings,
            ],
            'top_content' => [
                'post' => $topVotedPost,
                'photo' => $topRatedPhoto,
            ],
            'trend' => $this->buildMonthlyTrend($user),
        ];
    }

    /**
     * @return array<int, array{period: string, label: string, post_votes: int, photo_comments: int, photo_ratings: int, total_engagement: int, intensity: int}>
     */
    private function buildMonthlyTrend(User $user): array
    {
        $startDate = now()->startOfMonth()->subMonths(5);
        $endDate = now()->endOfMonth();

        $postVotes = $this->engagementCountsByMonth(
            table: 'post_votes',
            ownedContentTable: 'posts',
            joinColumn: 'post_id',
            ownerId: $user->id,
            startDate: $startDate,
            endDate: $endDate,
        );

        $photoComments = $this->engagementCountsByMonth(
            table: 'photo_comments',
            ownedContentTable: 'photos',
            joinColumn: 'photo_id',
            ownerId: $user->id,
            startDate: $startDate,
            endDate: $endDate,
        );

        $photoRatings = $this->engagementCountsByMonth(
            table: 'photo_ratings',
            ownedContentTable: 'photos',
            joinColumn: 'photo_id',
            ownerId: $user->id,
            startDate: $startDate,
            endDate: $endDate,
        );

        $months = collect(range(0, 5))
            ->map(fn (int $offset): Carbon => $startDate->copy()->addMonths($offset));

        $trend = $months->map(function (Carbon $month) use ($postVotes, $photoComments, $photoRatings): array {
            $period = $month->format('Y-m');
            $postVoteCount = (int) ($postVotes[$period] ?? 0);
            $photoCommentCount = (int) ($photoComments[$period] ?? 0);
            $photoRatingCount = (int) ($photoRatings[$period] ?? 0);
            $totalEngagement = $postVoteCount + $photoCommentCount + $photoRatingCount;

            return [
                'period' => $period,
                'label' => $month->format('M Y'),
                'post_votes' => $postVoteCount,
                'photo_comments' => $photoCommentCount,
                'photo_ratings' => $photoRatingCount,
                'total_engagement' => $totalEngagement,
            ];
        })->values();

        $maxEngagement = max(1, (int) $trend->max('total_engagement'));

        return $trend
            ->map(function (array $point) use ($maxEngagement): array {
                $point['intensity'] = (int) round(($point['total_engagement'] / $maxEngagement) * 100);

                return $point;
            })
            ->all();
    }

    /**
     * @return Collection<string, int>
     */
    private function engagementCountsByMonth(
        string $table,
        string $ownedContentTable,
        string $joinColumn,
        int $ownerId,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {
        $monthExpression = $this->monthBucketExpression(sprintf('%s.created_at', $table));

        return DB::table($table)
            ->join($ownedContentTable, sprintf('%s.id', $ownedContentTable), '=', sprintf('%s.%s', $table, $joinColumn))
            ->where(sprintf('%s.user_id', $ownedContentTable), $ownerId)
            ->whereBetween(sprintf('%s.created_at', $table), [$startDate, $endDate])
            ->selectRaw(sprintf('%s as period, COUNT(*) as total', $monthExpression))
            ->groupByRaw($monthExpression)
            ->pluck('total', 'period')
            ->map(fn ($total): int => (int) $total);
    }

    private function monthBucketExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => sprintf("strftime('%%Y-%%m', %s)", $column),
            'pgsql' => sprintf("to_char(%s, 'YYYY-MM')", $column),
            default => sprintf("DATE_FORMAT(%s, '%%Y-%%m')", $column),
        };
    }
}
