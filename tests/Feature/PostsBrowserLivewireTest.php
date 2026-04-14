<?php

namespace Tests\Feature;

use App\Livewire\Posts\Browser;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostsBrowserLivewireTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_posts_browser_only_renders_authenticated_user_posts(): void
    {
        $owner = User::factory()->user()->create();
        $other = User::factory()->user()->create();

        Post::factory()->for($owner)->create(['title' => 'Owner Story']);
        Post::factory()->for($other)->create(['title' => 'Other Story']);

        $this->actingAs($owner);

        Livewire::test(Browser::class)
            ->assertSee('Owner Story')
            ->assertDontSee('Other Story');
    }

    public function test_posts_browser_filters_posts_by_search_term(): void
    {
        $owner = User::factory()->user()->create();

        Post::factory()->for($owner)->create(['title' => 'Road Trip']);
        Post::factory()->for($owner)->create(['title' => 'Family Dinner']);

        $this->actingAs($owner);

        Livewire::test(Browser::class)
            ->set('search', 'Road')
            ->assertSee('Road Trip')
            ->assertDontSee('Family Dinner');
    }
}
