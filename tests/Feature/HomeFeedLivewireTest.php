<?php

namespace Tests\Feature;

use App\Livewire\Home\Feed;
use App\Models\Milestone;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeFeedLivewireTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_feed_search_matches_item_specific_text_fields(): void
    {
        $author = User::factory()->user()->create([
            'first_name' => 'Alicia',
            'last_name' => 'Stone',
        ]);

        Milestone::factory()->for($author)->create([
            'label' => 'Stargazer Award',
            'description' => 'Recognized for exceptional collaboration.',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Milestone::factory()->for($author)->create([
            'label' => 'Hidden Milestone',
            'description' => 'This should not appear in keyword results.',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Milestone::factory()->for($author)->create([
            'label' => 'Stargazer Internal',
            'description' => 'Private milestone must never appear.',
            'photo_id' => null,
            'is_public' => false,
        ]);

        Livewire::test(Feed::class)
            ->set('search', 'Stargazer')
            ->assertSee('Stargazer Award')
            ->assertDontSee('Hidden Milestone')
            ->assertDontSee('Stargazer Internal');
    }

    public function test_home_feed_type_filter_updates_results_live(): void
    {
        $author = User::factory()->user()->create();

        Post::factory()->for($author)->create(['title' => 'Public Post Result']);
        Milestone::factory()->for($author)->create([
            'label' => 'Public Milestone Result',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Livewire::test(Feed::class)
            ->assertSee('Public Post Result')
            ->assertSee('Public Milestone Result')
            ->set('type', 'milestone')
            ->assertSee('Public Milestone Result')
            ->assertDontSee('Public Post Result');
    }

    public function test_home_feed_resets_pagination_when_search_changes(): void
    {
        $author = User::factory()->user()->create();

        Post::factory()->for($author)->create(['title' => 'Needle Post']);
        Post::factory()->count(20)->for($author)->create();

        Livewire::test(Feed::class)
            ->set('type', 'post')
            ->call('setPage', 2)
            ->assertDontSee('Needle Post')
            ->set('search', 'Needle')
            ->assertSee('Needle Post');
    }

    public function test_home_feed_hydrates_search_and_filters_from_query_string(): void
    {
        $author = User::factory()->user()->create();

        Post::factory()->for($author)->create(['title' => 'Hydrated Query Post']);
        Milestone::factory()->for($author)->create([
            'label' => 'Hydrated Query Milestone',
            'photo_id' => null,
            'is_public' => true,
        ]);

        Livewire::withQueryParams([
            'search' => 'Hydrated',
            'type' => 'post',
            'sort' => 'date_asc',
        ])->test(Feed::class)
            ->assertSet('search', 'Hydrated')
            ->assertSet('type', 'post')
            ->assertSet('sort', 'date_asc')
            ->assertSee('Hydrated Query Post')
            ->assertDontSee('Hydrated Query Milestone');
    }

    public function test_home_feed_uses_responsive_masonry_card_layout(): void
    {
        $author = User::factory()->user()->create();

        Post::factory()->for($author)->create(['title' => 'Masonry Layout Post']);

        Livewire::test(Feed::class)
            ->assertSee('columns-1', false)
            ->assertSee('md:columns-2', false)
            ->assertSee('xl:columns-3', false)
            ->assertSee('2xl:columns-4', false)
            ->assertSee('break-inside-avoid', false);
    }
}
