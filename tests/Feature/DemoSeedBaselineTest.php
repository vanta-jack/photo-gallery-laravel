<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSeedBaselineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_database_seeder_creates_clean_two_user_baseline(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('photos', 0);
        $this->assertDatabaseCount('albums', 0);
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('photo_ratings', 0);
        $this->assertDatabaseCount('photo_comments', 0);
        $this->assertDatabaseCount('post_votes', 0);
        $this->assertDatabaseCount('guestbook_entries', 0);

        $expectedUsers = [
            'user@domain.com' => 'user',
            'admin@domain.com' => 'admin',
        ];

        foreach ($expectedUsers as $email => $role) {
            $user = User::query()->where('email', $email)->first();

            $this->assertNotNull($user);
            $this->assertSame($role, $user->role);
            $this->assertNull($user->profile_photo_id);
            $this->assertTrue(Hash::check('password', $user->password));
            $this->assertIsArray($user->academic_history);
            $this->assertIsArray($user->professional_experience);
            $this->assertIsArray($user->skills);
            $this->assertIsArray($user->certifications);
            $this->assertIsArray($user->other_links);
            $this->assertNotEmpty($user->bio);
            $this->assertNotEmpty($user->linkedin);
            $this->assertNotEmpty($user->github);
            $this->assertNotEmpty($user->orcid_id);
        }
    }

    public function test_database_seeder_creates_photo_optional_lifecycle_milestones_for_demo_users(): void
    {
        $this->seed();

        $users = User::query()
            ->whereIn('email', ['user@domain.com', 'admin@domain.com'])
            ->get();

        $this->assertCount(2, $users);
        $this->assertSame(16, Milestone::query()->count());
        $this->assertSame(0, Milestone::query()->whereNotNull('photo_id')->count());

        $expectedCategories = [
            'Baby',
            'Toddler',
            'Preschool',
            'Grade School',
            'Middle School',
            'High School',
            'College',
            'Adult',
        ];
        $expectedStages = [
            'baby',
            'toddler',
            'preschool',
            'grade_school',
            'middle_school',
            'high_school',
            'college',
            'adult',
        ];

        foreach ($users as $user) {
            $milestones = $user->milestones()
                ->orderBy('created_at')
                ->get();

            $this->assertCount(8, $milestones);
            $this->assertSame(
                $expectedCategories,
                $milestones->map(fn (Milestone $milestone): string => explode(' · ', $milestone->label)[0])->all(),
            );
            $this->assertSame(
                $expectedStages,
                $milestones->pluck('stage')->all(),
            );

            for ($index = 1; $index < $milestones->count(); $index++) {
                $this->assertTrue($milestones[$index]->created_at->gt($milestones[$index - 1]->created_at));
            }
        }
    }
}
