<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts used in local/demo environments.
     *
     * @var array<int, array{email: string, role: string}>
     */
    private const DEMO_USERS = [
        ['email' => 'user@domain.com', 'role' => 'user'],
        ['email' => 'admin@domain.com', 'role' => 'admin'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seededUserIds = collect(self::DEMO_USERS)
            ->map(function (array $demoUser): int {
                $attributes = User::factory()->withCvProfile()->raw([
                    'email' => $demoUser['email'],
                    'role' => $demoUser['role'],
                    'password' => 'password',
                    'profile_photo_id' => null,
                ]);

                return User::query()->updateOrCreate(
                    ['email' => $demoUser['email']],
                    $attributes,
                )->id;
            });

        User::query()
            ->whereNotIn('id', $seededUserIds)
            ->delete();
    }
}
