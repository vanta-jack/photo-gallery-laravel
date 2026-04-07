<?php

namespace Database\Seeders;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Chronological lifecycle placeholders mapped to existing milestone stages.
     *
     * @var array<int, array{stage: string, label: string, description: string, years_ago: int}>
     */
    private const LIFE_CYCLE_MILESTONES = [
        [
            'stage' => 'baby',
            'label' => 'Baby · First Smile',
            'description' => 'A gentle newborn milestone used as an early-life demo placeholder.',
            'years_ago' => 22,
        ],
        [
            'stage' => 'baby',
            'label' => 'Toddler · First Steps',
            'description' => 'Toddler stage placeholder for first independent steps.',
            'years_ago' => 21,
        ],
        [
            'stage' => 'grade_school',
            'label' => 'Preschool · First Day of Preschool',
            'description' => 'Preschool transition milestone placeholder.',
            'years_ago' => 19,
        ],
        [
            'stage' => 'grade_school',
            'label' => 'Grade School · Grade 1 Kickoff',
            'description' => 'Elementary school start marker for demo timelines.',
            'years_ago' => 17,
        ],
        [
            'stage' => 'grade_school',
            'label' => 'Middle School · Science Fair Finalist',
            'description' => 'Middle school achievement placeholder.',
            'years_ago' => 12,
        ],
        [
            'stage' => 'highschool_college',
            'label' => 'High School · Freshman Orientation',
            'description' => 'High school entry marker in the normalized lifecycle sequence.',
            'years_ago' => 8,
        ],
        [
            'stage' => 'highschool_college',
            'label' => 'College · Capstone Presentation',
            'description' => 'College completion milestone placeholder.',
            'years_ago' => 3,
        ],
        [
            'stage' => 'highschool_college',
            'label' => 'Adult · First Career Role',
            'description' => 'Adult-phase placeholder milestone for post-college progression.',
            'years_ago' => 1,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->whereIn('email', ['user@domain.com', 'admin@domain.com'])
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        Milestone::query()
            ->whereIn('user_id', $users->pluck('id'))
            ->delete();

        foreach ($users as $user) {
            foreach (self::LIFE_CYCLE_MILESTONES as $index => $milestone) {
                $timestamp = now()
                    ->startOfDay()
                    ->subYears($milestone['years_ago'])
                    ->addDays($index * 10);

                Milestone::query()->create([
                    'user_id' => $user->id,
                    'photo_id' => null,
                    'stage' => $milestone['stage'],
                    'label' => $milestone['label'],
                    'description' => $milestone['description'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }
}
