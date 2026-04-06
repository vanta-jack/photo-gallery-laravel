<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Toggle theme preference.
     * 
     * Cycles through: device → dark → light → device
     * Storage is handled by client-side localStorage in theme.js
     * This endpoint is called for logging/analytics if needed.
     * 
     * Returns JSON for AJAX calls or redirects for form submission.
     */
    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        // The actual theme switching is handled by theme.js in localStorage
        // This endpoint can be used for:
        // - Logging theme changes
        // - Analytics
        // - Future server-side logic
        
        // If AJAX request, return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Theme toggle handled by client-side localStorage',
                'timestamp' => now(),
            ]);
        }

        // Otherwise redirect back with message
        return back()->with('status', 'Theme preference updated');
    }
}
