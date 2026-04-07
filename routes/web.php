<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GuestbookEntryController;
use App\Http\Controllers\HomeController;
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
 * Pattern: Mixed access by content type
 * - Public: homepage, post details, guestbook feed/create/store, public profiles, photo analytics
 * - Auth-only: personal list pages (photos, albums, posts) and all edit/update/delete actions
 * - Policies enforce ownership/admin checks
 */

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

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
 * Core Content Routes
 *
 * Mix of public and authenticated read paths:
 * - Public browsing: analytics, photo/post/album details, guestbook, profiles
 * - Auth-only personal listing pages: photos, albums, posts
 */
Route::get('photos', [PhotoController::class, 'index'])->middleware('auth')->name('photos.index');
Route::get('photos/analytics', [PhotoAnalyticsController::class, 'index'])->name('photos.analytics');
Route::get('photos/create', [PhotoController::class, 'create'])->name('photos.create');
Route::post('photos', [PhotoController::class, 'store'])->name('photos.store');
Route::get('photos/{photo}', [PhotoController::class, 'show'])
    ->whereNumber('photo')
    ->name('photos.show');

Route::get('albums', [AlbumController::class, 'index'])->middleware('auth')->name('albums.index');
Route::get('albums/{album}', [AlbumController::class, 'show'])
    ->whereNumber('album')
    ->name('albums.show');

Route::get('posts', [PostController::class, 'index'])->middleware('auth')->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])
    ->whereNumber('post')
    ->name('posts.show');

Route::get('guestbook', [GuestbookEntryController::class, 'index'])->name('guestbook.index');
Route::get('guestbook/create', [GuestbookEntryController::class, 'create'])->name('guestbook.create');
Route::post('guestbook', [GuestbookEntryController::class, 'store'])->name('guestbook.store');
Route::get('users/{user}', [UserController::class, 'show'])
    ->whereNumber('user')
    ->name('users.show');

/**
 * Authenticated Routes
 *
 * These routes require login and use policies for authorization
 */
Route::middleware(['auth'])->group(function () {

    // Photo CRUD (except index/show/create/store handled above)
    Route::resource('photos', PhotoController::class)->except(['index', 'show', 'create', 'store']);

    // Album CRUD (except index/show handled above)
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

    // Post CRUD (except index/show handled above)
    Route::resource('posts', PostController::class)->except(['index', 'show']);

    // Guestbook CRUD (public index/create/store, auth required for edit/update/delete)
    Route::resource('guestbook', GuestbookEntryController::class)->except(['index', 'create', 'store']);

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
    Route::get('profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserController::class, 'update'])->name('profile.update');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:view-admin-dashboard'])
    ->group(function (): void {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::delete('posts/{post}', [AdminDashboardController::class, 'destroyPost'])->name('posts.destroy');
        Route::delete('photos/{photo}', [AdminDashboardController::class, 'destroyPhoto'])->name('photos.destroy');
        Route::delete('albums/{album}', [AdminDashboardController::class, 'destroyAlbum'])->name('albums.destroy');
        Route::delete('milestones/{milestone}', [AdminDashboardController::class, 'destroyMilestone'])->name('milestones.destroy');
        Route::delete('guestbook/{guestbook}', [AdminDashboardController::class, 'destroyGuestbookEntry'])->name('guestbook.destroy');
    });

// Theme toggle (available to all users, authenticated or not)
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
