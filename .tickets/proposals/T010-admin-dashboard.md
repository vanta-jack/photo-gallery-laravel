# T010: Admin Dashboard - User Analytics

**Priority:** Medium  
**Type:** Feature  
**Estimated Effort:** Medium  
**Depends on:** T001 (Brand Kit)

## Summary

Create an admin-only dashboard displaying user analytics: concurrent users, user registration over time, and website traffic metrics using existing database tables.

## Current State

- No admin dashboard exists
- User model has `role` field (supports 'guest', 'user', 'admin')
- `sessions` table exists with `user_id`, `last_activity` columns
- `users` table has `created_at` timestamps
- No admin routes or controllers exist

## Requirements

From `.tickets/active/004-site-implementations.md`:
> TODO: Innovate by adding an admin dashboard that tracks number of online concurrent users, users over time/website traffic

> Use what is available in the schema, do not add anything more. Use the user timestamps and session timestamps.

## Implementation Steps

### 1. Create Admin Controller Directory and Dashboard Controller

Create `app/Http/Controllers/Admin/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin analytics dashboard.
     */
    public function index(): View
    {
        // Concurrent/online users (sessions active in last 15 minutes)
        $onlineCount = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(15)->timestamp)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        
        // Total active sessions (including guests)
        $totalSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(15)->timestamp)
            ->count();
        
        // Users registered per day (last 30 days)
        $usersOverTime = User::query()
            ->selectRaw("DATE(created_at) as date, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
            ]);
        
        // Total registered users
        $totalUsers = User::count();
        
        // Users by role
        $usersByRole = User::query()
            ->selectRaw("role, COUNT(*) as count")
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
        
        // Sessions per day (last 7 days) - using SQLite datetime function
        $sessionsPerDay = DB::table('sessions')
            ->selectRaw("DATE(datetime(last_activity, 'unixepoch')) as date, COUNT(*) as count")
            ->where('last_activity', '>=', now()->subDays(7)->timestamp)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
            ]);
        
        // Recent registrations (last 10 users)
        $recentUsers = User::query()
            ->select('id', 'first_name', 'last_name', 'email', 'role', 'created_at')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'onlineCount',
            'totalSessions',
            'usersOverTime',
            'totalUsers',
            'usersByRole',
            'sessionsPerDay',
            'recentUsers'
        ));
    }
}
```

### 2. Add Policy Method in `app/Policies/UserPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // ... existing methods ...

    /**
     * Determine if user can view the admin dashboard.
     */
    public function viewAdminDashboard(User $user): bool
    {
        return $user->role === 'admin';
    }
}
```

### 3. Add Admin Routes in `routes/web.php`

```php
use App\Http\Controllers\Admin\DashboardController;

// Admin routes (requires authentication and admin role)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->can('viewAdminDashboard', User::class);
});
```

### 4. Create `resources/views/admin/dashboard.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <span class="text-sm text-muted-foreground">Analytics overview</span>
    </div>

    {{-- Key Metrics Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-card border border-border rounded p-4">
            <p class="text-sm text-muted-foreground">Online Users</p>
            <p class="text-3xl font-bold">{{ $onlineCount }}</p>
            <p class="text-xs text-muted-foreground">Last 15 minutes</p>
        </div>
        
        <div class="bg-card border border-border rounded p-4">
            <p class="text-sm text-muted-foreground">Active Sessions</p>
            <p class="text-3xl font-bold">{{ $totalSessions }}</p>
            <p class="text-xs text-muted-foreground">Including guests</p>
        </div>
        
        <div class="bg-card border border-border rounded p-4">
            <p class="text-sm text-muted-foreground">Total Users</p>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
            <p class="text-xs text-muted-foreground">Registered accounts</p>
        </div>
        
        <div class="bg-card border border-border rounded p-4">
            <p class="text-sm text-muted-foreground">Users by Role</p>
            <div class="text-sm mt-2">
                @foreach($usersByRole as $role => $count)
                    <div class="flex justify-between">
                        <span class="capitalize">{{ $role }}</span>
                        <span class="font-bold">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- User Registration Chart --}}
    <div class="bg-card border border-border rounded p-4">
        <h2 class="text-xl font-bold mb-4">User Registrations (Last 30 Days)</h2>
        
        @if($usersOverTime->isEmpty())
            <p class="text-muted-foreground">No registration data available.</p>
        @else
            <div class="space-y-2">
                @php
                    $maxCount = $usersOverTime->max('count') ?: 1;
                @endphp
                @foreach($usersOverTime as $day)
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-muted-foreground w-24">{{ $day['date'] }}</span>
                        <div class="flex-1 bg-muted rounded h-4 overflow-hidden">
                            <div 
                                class="bg-primary h-full transition-all"
                                style="width: {{ ($day['count'] / $maxCount) * 100 }}%"
                            ></div>
                        </div>
                        <span class="text-sm font-bold w-8 text-right">{{ $day['count'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Session Activity Chart --}}
    <div class="bg-card border border-border rounded p-4">
        <h2 class="text-xl font-bold mb-4">Session Activity (Last 7 Days)</h2>
        
        @if($sessionsPerDay->isEmpty())
            <p class="text-muted-foreground">No session data available.</p>
        @else
            <div class="space-y-2">
                @php
                    $maxSessions = $sessionsPerDay->max('count') ?: 1;
                @endphp
                @foreach($sessionsPerDay as $day)
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-muted-foreground w-24">{{ $day['date'] }}</span>
                        <div class="flex-1 bg-muted rounded h-4 overflow-hidden">
                            <div 
                                class="bg-secondary-foreground h-full transition-all"
                                style="width: {{ ($day['count'] / $maxSessions) * 100 }}%"
                            ></div>
                        </div>
                        <span class="text-sm font-bold w-8 text-right">{{ $day['count'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Registrations --}}
    <div class="bg-card border border-border rounded p-4">
        <h2 class="text-xl font-bold mb-4">Recent Registrations</h2>
        
        @if($recentUsers->isEmpty())
            <p class="text-muted-foreground">No users registered.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border">
                        <th class="text-left py-2 font-bold">Name</th>
                        <th class="text-left py-2 font-bold">Email</th>
                        <th class="text-left py-2 font-bold">Role</th>
                        <th class="text-left py-2 font-bold">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                        <tr class="border-b border-border">
                            <td class="py-2">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="py-2 text-muted-foreground">{{ $user->email }}</td>
                            <td class="py-2">
                                <span class="bg-secondary text-secondary-foreground text-xs px-2 py-0.5 rounded">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-2 text-muted-foreground">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
```

### 5. Add Admin Dashboard Link in Header (for admins only)

Update `resources/views/layouts/partials/header.blade.php`:

```blade
@auth
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Admin</a>
    @endif
@endauth
```

## Files to Create/Modify

| File | Action |
|------|--------|
| `app/Http/Controllers/Admin/DashboardController.php` | Create |
| `app/Policies/UserPolicy.php` | Modify |
| `routes/web.php` | Modify |
| `resources/views/admin/dashboard.blade.php` | Create |
| `resources/views/layouts/partials/header.blade.php` | Modify |

## Acceptance Criteria

- [ ] Only admin users can access `/admin/dashboard`
- [ ] Non-admin users see 403 Forbidden
- [ ] Online user count shows users active in last 15 minutes
- [ ] User registration chart shows data for last 30 days
- [ ] Session activity chart shows data for last 7 days
- [ ] Users by role breakdown is accurate
- [ ] Recent registrations table shows last 10 users
- [ ] Admin link appears in navigation for admin users only

## Dependencies

- T001 (Brand Kit) - for consistent styling

## Notes

- Uses existing `sessions` table for activity tracking (no additional tables)
- SQLite datetime functions used for session timestamps
- Pure CSS bar charts (no JavaScript charting library needed)
- Policy-based authorization follows Laravel 13 best practices
- All queries use existing columns only per requirements
