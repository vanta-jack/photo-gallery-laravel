<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserPublicProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_profile_route_renders_for_guests(): void
    {
        $user = User::factory()->user()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'bio' => 'I build elegant computing systems.',
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSeeText('Ada Lovelace');
        $response->assertSeeText('Contact Me');
    }

    public function test_public_profile_route_requires_numeric_user_identifier(): void
    {
        $this->get('/users/not-a-number')->assertNotFound();
    }

    public function test_contact_modal_hides_private_phone_and_shows_public_contact_fields(): void
    {
        $user = User::factory()->user()->create([
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'grace@example.com',
            'phone' => '+1 555 0100',
            'phone_public' => false,
            'linkedin' => 'https://linkedin.com/in/gracehopper',
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSeeText('grace@example.com');
        $response->assertSeeText('https://linkedin.com/in/gracehopper');
        $response->assertDontSeeText('+1 555 0100');
    }

    public function test_contact_modal_shows_phone_when_public(): void
    {
        $user = User::factory()->user()->create([
            'first_name' => 'Katherine',
            'last_name' => 'Johnson',
            'phone' => '+1 555 0199',
            'phone_public' => true,
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk();
        $response->assertSeeText('+1 555 0199');
    }
}
