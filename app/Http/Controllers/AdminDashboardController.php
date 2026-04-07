<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $activeThreshold = now()->subMinutes(15)->timestamp;
        $windowStart = now()->startOfDay()->subDays(13);

        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', $activeThreshold);

        $onlineUsers = (clone $activeSessions)
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');

        $concurrentSessions = (clone $activeSessions)->count();
        $guestSessions = (clone $activeSessions)->whereNull('user_id')->count();

        $userTotals = User::query()
            ->selectRaw('COUNT(*) as total_users')
            ->selectRaw("SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users")
            ->selectRaw("SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as regular_users")
            ->selectRaw("SUM(CASE WHEN role = 'guest' THEN 1 ELSE 0 END) as guest_users")
            ->toBase()
            ->first();

        $registrationCounts = $this->countsByDay(
            table: 'users',
            column: 'created_at',
            startDate: $windowStart,
        );

        $sessionTrafficCounts = $this->countsByDay(
            table: 'sessions',
            column: 'last_activity',
            startDate: $windowStart,
            unixTimestamp: true,
        );

        return view('admin.dashboard', [
            'liveSessions' => [
                'online_users' => (int) $onlineUsers,
                'concurrent_sessions' => (int) $concurrentSessions,
                'guest_sessions' => (int) $guestSessions,
            ],
            'roleBreakdown' => [
                'total_users' => (int) ($userTotals->total_users ?? 0),
                'admin_users' => (int) ($userTotals->admin_users ?? 0),
                'regular_users' => (int) ($userTotals->regular_users ?? 0),
                'guest_users' => (int) ($userTotals->guest_users ?? 0),
            ],
            'contentTotals' => [
                'photos' => Photo::query()->count(),
                'albums' => Album::query()->count(),
                'posts' => Post::query()->count(),
            ],
            'registrations' => $this->buildDailySeries($registrationCounts, $windowStart),
            'sessionTraffic' => $this->buildDailySeries($sessionTrafficCounts, $windowStart),
        ]);
    }

    /**
     * @return Collection<string, int>
     */
    private function countsByDay(string $table, string $column, Carbon $startDate, bool $unixTimestamp = false): Collection
    {
        $dayExpression = $this->dayBucketExpression($column, $unixTimestamp);

        $query = DB::table($table)
            ->selectRaw(sprintf('%s as period, COUNT(*) as total', $dayExpression))
            ->groupByRaw($dayExpression)
            ->orderBy('period');

        if ($unixTimestamp) {
            $query->where($column, '>=', $startDate->timestamp);
        } else {
            $query->where($column, '>=', $startDate);
        }

        return $query
            ->pluck('total', 'period')
            ->map(fn (mixed $total): int => (int) $total);
    }

    /**
     * @param  Collection<string, int>  $counts
     * @return array<int, array{period: string, label: string, total: int, intensity: int}>
     */
    private function buildDailySeries(Collection $counts, Carbon $startDate): array
    {
        $days = collect(range(0, 13))
            ->map(fn (int $offset): Carbon => $startDate->copy()->addDays($offset));

        $series = $days->map(function (Carbon $date) use ($counts): array {
            $period = $date->format('Y-m-d');
            $total = (int) ($counts[$period] ?? 0);

            return [
                'period' => $period,
                'label' => $date->format('M j'),
                'total' => $total,
            ];
        });

        $maxTotal = max(1, (int) $series->max('total'));

        return $series
            ->map(function (array $point) use ($maxTotal): array {
                $point['intensity'] = (int) round(($point['total'] / $maxTotal) * 100);

                return $point;
            })
            ->all();
    }

    private function dayBucketExpression(string $column, bool $unixTimestamp = false): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => $unixTimestamp
                ? sprintf("strftime('%%Y-%%m-%%d', datetime(%s, 'unixepoch'))", $column)
                : sprintf("strftime('%%Y-%%m-%%d', %s)", $column),
            'pgsql' => $unixTimestamp
                ? sprintf("to_char(to_timestamp(%s), 'YYYY-MM-DD')", $column)
                : sprintf("to_char(%s, 'YYYY-MM-DD')", $column),
            default => $unixTimestamp
                ? sprintf('DATE(FROM_UNIXTIME(%s))', $column)
                : sprintf('DATE(%s)', $column),
        };
    }
}
