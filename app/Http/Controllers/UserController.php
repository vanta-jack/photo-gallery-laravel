<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * UserController
 * 
 * Manages user profile updates.
 * Basic implementation for editing name, email, profile photo.
 */
class UserController extends Controller
{
    /**
     * Show profile edit form
     * 
     * Defaults to editing current user's profile
     */
    public function edit(User $user = null): View
    {
        // If no user specified, edit current user
        $user = $user ?? auth()->user();
        
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(UpdateUserRequest $request, User $user = null): RedirectResponse
    {
        // If no user specified, update current user
        $user = $user ?? auth()->user();
        
        $this->authorize('update', $user);

        $user->update($request->validated());

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Profile updated successfully!');
    }
}
