<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_update_basic_cv_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'bio' => null,
            'phone' => null,
            'linkedin' => null,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'bio' => 'Experienced software engineer with expertise in Laravel and Vue.js.',
            'phone' => '+1 (555) 123-4567',
            'phone_public' => false,
            'linkedin' => 'https://linkedin.com/in/johndoe',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertEquals('Experienced software engineer with expertise in Laravel and Vue.js.', $user->bio);
        $this->assertEquals('+1 (555) 123-4567', $user->phone);
        $this->assertFalse($user->phone_public);
        $this->assertEquals('https://linkedin.com/in/johndoe', $user->linkedin);
    }

    public function test_phone_visibility_toggle_works(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Set phone public
        $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => '+1 (555) 123-4567',
            'phone_public' => true,
        ]);

        $user->refresh();
        $this->assertTrue($user->phone_public);

        // Set phone private
        $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => '+1 (555) 123-4567',
            'phone_public' => false,
        ]);

        $user->refresh();
        $this->assertFalse($user->phone_public);
    }

    public function test_academic_history_validation(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Invalid: missing institution when academic_history is present
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'academic_history' => [
                ['degree' => 'BSc Computer Science', 'institution' => '', 'graduation_date' => '2020-06-01'],
            ],
        ]);

        $response->assertSessionHasErrors('academic_history.0.institution');

        // Valid academic history
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'academic_history' => [
                ['degree' => 'BSc Computer Science', 'institution' => 'MIT', 'graduation_date' => '2020-06-01'],
                ['degree' => 'MSc Data Science', 'institution' => 'Stanford', 'graduation_date' => '2022-05-15'],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertCount(2, $user->academic_history);
        $this->assertEquals('MIT', $user->academic_history[0]['institution']);
    }

    public function test_professional_experience_validation(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Invalid: missing company when professional_experience is present
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'professional_experience' => [
                ['title' => 'Senior Developer', 'company' => '', 'start_date' => '2020-01-01', 'end_date' => '2023-12-31'],
            ],
        ]);

        $response->assertSessionHasErrors('professional_experience.0.company');

        // Valid professional experience
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'professional_experience' => [
                [
                    'title' => 'Senior Developer',
                    'company' => 'Tech Corp',
                    'start_date' => '2020-01-01',
                    'end_date' => '2023-12-31',
                    'description' => 'Led development of core platform features.',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertCount(1, $user->professional_experience);
        $this->assertEquals('Tech Corp', $user->professional_experience[0]['company']);
    }

    public function test_skills_array_validation(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'skills' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js', 'MySQL'],
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertCount(5, $user->skills);
        $this->assertContains('Laravel', $user->skills);
        $this->assertContains('Vue.js', $user->skills);
    }

    public function test_json_fields_persist_correctly(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $academicData = [
            ['degree' => 'BSc CS', 'institution' => 'MIT', 'graduation_date' => '2020-06-01'],
        ];
        $experienceData = [
            ['title' => 'Developer', 'company' => 'Acme', 'start_date' => '2020-01-01', 'end_date' => '2023-12-31', 'description' => 'Built things'],
        ];
        $skillsData = ['PHP', 'Laravel', 'JavaScript'];
        $certificationsData = [
            ['name' => 'AWS Certified Developer', 'issuer' => 'Amazon', 'awarded_on' => '2024-03-15'],
        ];
        $otherLinksData = [
            ['label' => 'Portfolio', 'url' => 'https://example.com'],
        ];

        $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'academic_history' => $academicData,
            'professional_experience' => $experienceData,
            'skills' => $skillsData,
            'certifications' => $certificationsData,
            'other_links' => $otherLinksData,
        ]);

        $user->refresh();
        
        // Verify data persists correctly as arrays
        $this->assertEquals($academicData, $user->academic_history);
        $this->assertEquals($experienceData, $user->professional_experience);
        $this->assertEquals($skillsData, $user->skills);
        $this->assertEquals($certificationsData, $user->certifications);
        $this->assertEquals($otherLinksData, $user->other_links);
        
        // Verify casts are working (should return arrays, not JSON strings)
        $this->assertIsArray($user->academic_history);
        $this->assertIsArray($user->professional_experience);
        $this->assertIsArray($user->skills);
        $this->assertIsArray($user->certifications);
        $this->assertIsArray($user->other_links);
    }

    public function test_profile_update_route_targets_authenticated_user(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user1)->put(route('profile.update', ['user' => $user2->id]), [
            'first_name' => 'Hacked',
            'last_name' => $user1->last_name,
            'email' => $user1->email,
        ]);

        $response->assertRedirect(route('profile.show'));
        $user1->refresh();
        $user2->refresh();

        // Route has no path parameter, so the authenticated user's profile is updated.
        $this->assertEquals('Hacked', $user1->first_name);
        $this->assertNotEquals('Hacked', $user2->first_name);
    }

    public function test_bio_max_length_validation(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Bio exceeds 5000 characters
        $longBio = str_repeat('a', 5001);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'bio' => $longBio,
        ]);

        $response->assertSessionHasErrors('bio');
    }

    public function test_orcid_and_github_fields_work(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'orcid_id' => '0000-0002-1234-5678',
            'github' => 'https://github.com/johndoe',
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertEquals('0000-0002-1234-5678', $user->orcid_id);
        $this->assertEquals('https://github.com/johndoe', $user->github);
    }

    public function test_authenticated_profile_defaults_to_read_only_with_edit_cta(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSeeText('Contact Me');
        $response->assertSeeText('Edit Profile');
        $response->assertSee(route('profile.edit'), false);
        $response->assertDontSee('id="profile-form"', false);
    }

    public function test_profile_route_requires_authentication(): void
    {
        $this->get(route('profile.show'))
            ->assertRedirect(route('login'));
    }
}
