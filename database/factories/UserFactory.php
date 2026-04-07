<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => fake()->randomElement(['guest', 'user', 'admin']),
            'email' => fake()->unique()->safeEmail(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'password' => static::$password ??= Hash::make('password'),
            'profile_photo_id' => null,
            'bio' => null,
            'phone' => null,
            'phone_public' => false,
            'linkedin' => null,
            'academic_history' => null,
            'professional_experience' => null,
            'skills' => null,
            'certifications' => null,
            'orcid_id' => null,
            'github' => null,
            'other_links' => null,
        ];
    }

    /**
     * Create a user with realistic profile and CV details.
     */
    public function withCvProfile(): static
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $handle = fake()->slug(2);

        return $this->state(fn (array $attributes) => [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'bio' => fake()->realTextBetween(180, 320),
            'phone' => fake()->e164PhoneNumber(),
            'phone_public' => fake()->boolean(20),
            'linkedin' => 'https://linkedin.com/in/'.$handle,
            'academic_history' => [
                [
                    'degree' => fake()->randomElement(['BSc Computer Science', 'BA Design', 'BEng Software Engineering']),
                    'institution' => fake()->company().' University',
                    'graduation_date' => fake()->date('Y-m-d', '-8 years'),
                ],
                [
                    'degree' => fake()->randomElement(['MSc Data Science', 'MSc Information Systems', 'MBA Technology Management']),
                    'institution' => fake()->company().' Institute',
                    'graduation_date' => fake()->date('Y-m-d', '-3 years'),
                ],
            ],
            'professional_experience' => [
                [
                    'title' => fake()->randomElement(['Software Engineer', 'Product Designer', 'Project Coordinator']),
                    'company' => fake()->company(),
                    'start_date' => fake()->date('Y-m-d', '-6 years'),
                    'end_date' => fake()->date('Y-m-d', '-3 years'),
                    'description' => fake()->sentence(12),
                ],
                [
                    'title' => fake()->randomElement(['Senior Engineer', 'Lead Designer', 'Engineering Manager']),
                    'company' => fake()->company(),
                    'start_date' => fake()->date('Y-m-d', '-3 years'),
                    'end_date' => null,
                    'description' => fake()->sentence(14),
                ],
            ],
            'skills' => fake()->randomElements([
                'PHP',
                'Laravel',
                'SQL',
                'JavaScript',
                'Vue.js',
                'Testing',
                'CI/CD',
                'System Design',
            ], fake()->numberBetween(5, 7)),
            'certifications' => [
                [
                    'name' => fake()->randomElement(['AWS Certified Developer', 'Google Cloud Professional', 'Scrum Master']),
                    'issuer' => fake()->company(),
                    'awarded_on' => fake()->date('Y-m-d', '-2 years'),
                ],
            ],
            'orcid_id' => sprintf(
                '%04d-%04d-%04d-%04d',
                fake()->numberBetween(0, 9999),
                fake()->numberBetween(0, 9999),
                fake()->numberBetween(0, 9999),
                fake()->numberBetween(0, 9999),
            ),
            'github' => 'https://github.com/'.fake()->userName(),
            'other_links' => [
                [
                    'label' => 'Portfolio',
                    'url' => 'https://example.com/'.fake()->slug(),
                ],
                [
                    'label' => 'Talks',
                    'url' => 'https://example.org/'.fake()->slug(),
                ],
            ],
        ]);
    }

    /**
     * Create a guest user (no email/password required).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'guest',
            'email' => null,
            'password' => null,
            'phone' => null,
            'linkedin' => null,
            'bio' => null,
            'academic_history' => null,
            'professional_experience' => null,
            'skills' => null,
            'certifications' => null,
            'orcid_id' => null,
            'github' => null,
            'other_links' => null,
        ]);
    }

    /**
     * Create a regular user.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
