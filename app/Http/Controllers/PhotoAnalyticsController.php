<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\View\View;

class PhotoAnalyticsController extends Controller
{
    public function index(): View
    {
        $topRatedPhotos = Photo::query()
            ->select(['id', 'user_id', 'path', 'title'])
            ->with('user:id,first_name,last_name')
            ->withCount(['ratings', 'comments'])
            ->withAvg('ratings as average_rating', 'rating')
            ->has('ratings')
            ->orderByDesc('average_rating')
            ->orderByDesc('ratings_count')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $mostCommentedPhotos = Photo::query()
            ->select(['id', 'user_id', 'path', 'title'])
            ->with('user:id,first_name,last_name')
            ->withCount(['comments', 'ratings'])
            ->withAvg('ratings as average_rating', 'rating')
            ->has('comments')
            ->orderByDesc('comments_count')
            ->orderByDesc('ratings_count')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('photos.analytics', [
            'topRatedPhotos' => $topRatedPhotos,
            'mostCommentedPhotos' => $mostCommentedPhotos,
            'mostCommentedScale' => max(1, (int) ($mostCommentedPhotos->max('comments_count') ?? 0)),
        ]);
    }
}
