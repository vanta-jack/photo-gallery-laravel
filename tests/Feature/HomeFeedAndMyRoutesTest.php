<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Milestone;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomeFeedAndMyRoutesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_my_routes_require_authentication(): void
    {
        $this->get(route('photos.index'))->assertRedirect(route('login'));
        $this->get(route('albums.index'))->assertRedirect(route('login'));
        $this->get(route('posts.index'))->assertRedirect(route('login'));
    }

    public function test_my_routes_only_show_authenticated_user_content(): void
    {
        $owner = User::factory()->user()->create();
        $other = User::factory()->user()->create();

        Post::factory()->for($owner)->create(['title' => 'Owner Post']);
        Post::factory()->for($other)->create(['title' => 'Other Post']);

        Album::factory()->for($owner)->create(['title' => 'Owner Album']);
        Album::factory()->for($other)->create(['title' => 'Other Album']);

        $this->actingAs($owner)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSeeText('Owner Post')
            ->assertDontSeeText('Other Post');

        $this->actingAs($owner)
            ->get(route('albums.index'))
            ->assertOk()
            ->assertSeeText('Owner Album')
            ->assertDontSeeText('Other Album');
    }

    public function test_home_feed_only_shows_public_milestones_and_respects_type_filter(): void
    {
        $user = User::factory()->user()->create();

        Milestone::factory()->for($user)->create([
            'label' => 'Public Milestone',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Milestone::factory()->for($user)->create([
            'label' => 'Private Milestone',
            'photo_id' => null,
            'is_public' => false,
        ]);

        Post::factory()->for($user)->create(['title' => 'Public Post']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Public Milestone')
            ->assertDontSeeText('Private Milestone')
            ->assertSeeText('Public Post');

        $this->get(route('home', ['type' => 'milestone']))
            ->assertOk()
            ->assertSeeText('Public Milestone')
            ->assertDontSeeText('Public Post');
    }
}
