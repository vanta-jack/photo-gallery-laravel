<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotoAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user();
        $requestedScope = $request->string('scope')->toString();
        $scope = in_array($requestedScope, ['global', 'mine'], true) ? $requestedScope : 'global';

        if ($viewer === null) {
            $scope = 'global';
        }

        $baseQuery = Photo::query()
            ->select(['id', 'user_id', 'path', 'title'])
            ->with('user:id,first_name,last_name')
            ->withCount(['ratings', 'comments'])
            ->withAvg('ratings as average_rating', 'rating')
            ->when($scope === 'mine' && $viewer !== null, fn ($query) => $query->whereBelongsTo($viewer));

        $topRatedPhotos = (clone $baseQuery)
            ->has('ratings')
            ->orderByDesc('average_rating')
            ->orderByDesc('ratings_count')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $mostCommentedPhotos = (clone $baseQuery)
            ->has('comments')
            ->orderByDesc('comments_count')
            ->orderByDesc('ratings_count')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('photos.analytics', [
            'topRatedPhotos' => $topRatedPhotos,
            'mostCommentedPhotos' => $mostCommentedPhotos,
            'scope' => $scope,
            'mostCommentedScale' => max(1, (int) ($mostCommentedPhotos->max('comments_count') ?? 0)),
            'topRatedChart' => $this->buildBarChartSeries(
                $topRatedPhotos->map(fn (Photo $photo): int => (int) round(((float) ($photo->average_rating ?? 0)) * 10))->all(),
                50
            ),
            'mostCommentedChart' => $this->buildBarChartSeries(
                $mostCommentedPhotos->pluck('comments_count')->map(fn ($count): int => (int) $count)->all(),
                max(1, (int) ($mostCommentedPhotos->max('comments_count') ?? 0)),
            ),
        ]);
    }

    /**
     * @param  array<int, int>  $values
     * @return array<int, array{x: float, width: float, height: float}>
     */
    private function buildBarChartSeries(array $values, int $scale): array
    {
        $count = max(1, count($values));
        $gap = 2;
        $width = (100 - (($count - 1) * $gap)) / $count;

        return collect($values)
            ->values()
            ->map(function (int $value, int $index) use ($gap, $width, $scale): array {
                $normalized = $scale > 0 ? min(100, max(0, ($value / $scale) * 100)) : 0;
                $x = ($index * ($width + $gap));

                return [
                    'x' => round($x, 2),
                    'width' => round($width, 2),
                    'height' => round($normalized, 2),
                ];
            })
            ->all();
    }
}
