<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_dashboard_redirects_guests_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_forbids_non_admin_users(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_dashboard_renders_expected_aggregated_metrics_for_admins(): void
    {
        Carbon::setTestNow('2026-05-10 12:00:00');

        try {
            $admin = User::factory()->admin()->create([
                'created_at' => '2026-05-09 08:00:00',
                'updated_at' => '2026-05-09 08:00:00',
            ]);
            $regularUser = User::factory()->user()->create([
                'created_at' => '2026-05-08 08:00:00',
                'updated_at' => '2026-05-08 08:00:00',
            ]);
            $guestUser = User::factory()->guest()->create([
                'created_at' => '2026-05-07 08:00:00',
                'updated_at' => '2026-05-07 08:00:00',
            ]);

            User::factory()->user()->create([
                'created_at' => '2026-04-01 08:00:00',
                'updated_at' => '2026-04-01 08:00:00',
            ]);

            Photo::factory()->for($regularUser)->count(2)->create();
            Album::factory()->for($regularUser)->create();
            Post::factory()->for($admin)->create();
            Post::factory()->for($regularUser)->create();

            DB::table('sessions')->insert([
                [
                    'id' => 'active-admin',
                    'user_id' => $admin->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'phpunit',
                    'payload' => 'payload',
                    'last_activity' => now()->subMinutes(2)->timestamp,
                ],
                [
                    'id' => 'active-user',
                    'user_id' => $regularUser->id,
                    'ip_address' => '127.0.0.2',
                    'user_agent' => 'phpunit',
                    'payload' => 'payload',
                    'last_activity' => now()->subMinutes(6)->timestamp,
                ],
                [
                    'id' => 'active-guest',
                    'user_id' => null,
                    'ip_address' => '127.0.0.3',
                    'user_agent' => 'phpunit',
                    'payload' => 'payload',
                    'last_activity' => now()->subMinutes(3)->timestamp,
                ],
                [
                    'id' => 'inactive-user',
                    'user_id' => $guestUser->id,
                    'ip_address' => '127.0.0.4',
                    'user_agent' => 'phpunit',
                    'payload' => 'payload',
                    'last_activity' => now()->subHour()->timestamp,
                ],
            ]);

            $response = $this->actingAs($admin)->get(route('admin.dashboard'));

            $response->assertOk();
            $response->assertSeeText('Admin Dashboard');
            $response->assertViewHas('liveSessions', fn (array $metrics): bool => $metrics === [
                'online_users' => 2,
                'concurrent_sessions' => 3,
                'guest_sessions' => 1,
            ]);
            $response->assertViewHas('roleBreakdown', fn (array $metrics): bool => $metrics === [
                'total_users' => 4,
                'admin_users' => 1,
                'regular_users' => 2,
                'guest_users' => 1,
            ]);
            $response->assertViewHas('contentTotals', fn (array $totals): bool => $totals === [
                'photos' => 2,
                'albums' => 1,
                'posts' => 2,
            ]);
            $response->assertSeeText('Registrations (14 days)');
            $response->assertSeeText('Session Traffic (14 days)');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_navigation_link_visibility_follows_role_rules(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->user()->create();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'), false);
    }
}
