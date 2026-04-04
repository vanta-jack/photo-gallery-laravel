<?php

namespace Database\Seeders;

use App\Models\Milestone;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $photos = Photo::all();

        // Create 20 milestones across all stages
        $stages = ['baby', 'grade_school', 'highschool_college'];
        $labels = [
            'baby' => ['Month 1', 'Month 6', 'Month 12', 'First Steps', 'First Words'],
            'grade_school' => ['Grade 1', 'Grade 3', 'Grade 5', 'First Day of School', 'Field Trip'],
            'highschool_college' => ['1st Year', '2nd Year', '3rd Year', 'Graduation', 'Prom Night'],
        ];

        for ($i = 0; $i < 20; $i++) {
            $stage = fake()->randomElement($stages);
            
            Milestone::factory()->create([
                'user_id' => $users->random()->id,
                'photo_id' => $photos->random()->id,
                'stage' => $stage,
                'label' => fake()->randomElement($labels[$stage]),
            ]);
        }
    }
}
