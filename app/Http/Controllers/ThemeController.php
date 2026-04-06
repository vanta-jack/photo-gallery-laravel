<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Toggle between light and dark theme.
     * Stores preference in both session and cookie.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $currentTheme = session('theme', 'dark');
        $newTheme = $currentTheme === 'dark' ? 'light' : 'dark';
        
        session(['theme' => $newTheme]);
        
        return back()->withCookie(
            cookie('theme', $newTheme, 60 * 24 * 365) // 1 year
        );
    }
}
