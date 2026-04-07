<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GuestbookEntryController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\PhotoAnalyticsController;
use App\Http\Controllers\PhotoCommentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PhotoRatingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Routes Configuration
 *
 * Pattern: Public viewing, authenticated actions
 * - Anyone can view photos, albums, posts, guestbook (index, show)
 * - Must be logged in to create, edit, delete
 * - Policies enforce ownership/admin checks
 */

// Homepage
Route::get('/', function () {
    return view('dashboard');
})->name('home');

/**
 * Authentication Routes
 */
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * Public Routes (no auth required)
 *
 * These routes allow anyone to browse content
 */
Route::get('photos', [PhotoController::class, 'index'])->name('photos.index');
Route::get('photos/analytics', [PhotoAnalyticsController::class, 'index'])->name('photos.analytics');
Route::get('photos/create', [PhotoController::class, 'create'])->name('photos.create');
Route::post('photos', [PhotoController::class, 'store'])->name('photos.store');
Route::get('photos/{photo}', [PhotoController::class, 'show'])
    ->whereNumber('photo')
    ->name('photos.show');

Route::get('albums', [AlbumController::class, 'index'])->name('albums.index');
Route::get('albums/{album}', [AlbumController::class, 'show'])
    ->whereNumber('album')
    ->name('albums.show');

Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])
    ->whereNumber('post')
    ->name('posts.show');

Route::get('guestbook', [GuestbookEntryController::class, 'index'])->name('guestbook.index');
Route::get('users/{user}', [UserController::class, 'show'])
    ->whereNumber('user')
    ->name('users.show');

/**
 * Authenticated Routes
 *
 * These routes require login and use policies for authorization
 */
Route::middleware(['auth'])->group(function () {

    // Photo CRUD (except index/show which are public)
    Route::resource('photos', PhotoController::class)->except(['index', 'show', 'create', 'store']);

    // Album CRUD (except index/show which are public)
    Route::resource('albums', AlbumController::class)->except(['index', 'show']);
    Route::post('albums/photos', [AlbumController::class, 'storePhotoForCreate'])
        ->name('albums.photos.create.store');
    Route::post('albums/{album}/photos', [AlbumController::class, 'storePhoto'])
        ->whereNumber('album')
        ->name('albums.photos.store');

    // Album batch operations
    Route::post('albums/batch/delete', [AlbumController::class, 'batchDelete'])->name('albums.batch.delete');
    Route::post('albums/batch/visibility', [AlbumController::class, 'batchUpdateVisibility'])->name('albums.batch.visibility');
    Route::post('albums/batch/favorite', [AlbumController::class, 'batchUpdateFavorite'])->name('albums.batch.favorite');

    // Post CRUD (except index/show which are public)
    Route::resource('posts', PostController::class)->except(['index', 'show']);

    // Guestbook CRUD (except index which is public)
    Route::resource('guestbook', GuestbookEntryController::class)->except(['index']);

    // Milestones (completely private - user only sees own)
    Route::resource('milestones', MilestoneController::class);

    // Nested resource routes
    // Comments on photos: /photos/{photo}/comments
    Route::resource('photos.comments', PhotoCommentController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Ratings on photos: /photos/{photo}/ratings
    Route::resource('photos.ratings', PhotoRatingController::class)
        ->only(['create', 'store', 'destroy']);

    // Votes on posts: /posts/{post}/votes
    Route::resource('posts.votes', PostVoteController::class)
        ->only(['create', 'store', 'destroy']);

    // User profile
    Route::get('profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserController::class, 'update'])->name('profile.update');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:view-admin-dashboard'])
    ->group(function (): void {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

// Theme toggle (available to all users, authenticated or not)
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
