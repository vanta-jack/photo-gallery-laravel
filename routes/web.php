<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GuestbookEntryController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\PhotoCommentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PhotoRatingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostVoteController;
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
 * Public Routes (no auth required)
 * 
 * These routes allow anyone to browse content
 */
Route::get('photos', [PhotoController::class, 'index'])->name('photos.index');
Route::get('photos/{photo}', [PhotoController::class, 'show'])->name('photos.show');

Route::get('albums', [AlbumController::class, 'index'])->name('albums.index');
Route::get('albums/{album}', [AlbumController::class, 'show'])->name('albums.show');

Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('guestbook', [GuestbookEntryController::class, 'index'])->name('guestbook.index');

/**
 * Authenticated Routes
 * 
 * These routes require login and use policies for authorization
 */
Route::middleware(['auth'])->group(function () {
    
    // Photo CRUD (except index/show which are public)
    Route::resource('photos', PhotoController::class)->except(['index', 'show']);
    
    // Album CRUD (except index/show which are public)
    Route::resource('albums', AlbumController::class)->except(['index', 'show']);
    
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


