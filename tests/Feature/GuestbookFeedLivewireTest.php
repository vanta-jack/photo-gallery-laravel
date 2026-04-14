<?php

namespace Tests\Feature;

use App\Livewire\Guestbook\Feed;
use App\Models\GuestbookEntry;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuestbookFeedLivewireTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guestbook_feed_component_filters_entries_by_search_term(): void
    {
        $author = User::factory()->user()->create();

        $summerPost = Post::factory()->for($author)->create([
            'title' => 'Summer Journal',
            'description' => 'Sunny afternoons in the park.',
        ]);
        $winterPost = Post::factory()->for($author)->create([
            'title' => 'Winter Notes',
            'description' => 'Snowy evenings by the fire.',
        ]);

        GuestbookEntry::factory()->create(['post_id' => $summerPost->id]);
        GuestbookEntry::factory()->create(['post_id' => $winterPost->id]);

        Livewire::test(Feed::class)
            ->assertSee('Summer Journal')
            ->assertSee('Winter Notes')
            ->set('search', 'Summer')
            ->assertSee('Summer Journal')
            ->assertDontSee('Winter Notes');
    }

    public function test_guestbook_feed_uses_home_shell_layout_patterns_and_keeps_guestbook_actions(): void
    {
        $author = User::factory()->user()->create();

        $post = Post::factory()->for($author)->create([
            'title' => 'Layout Entry',
            'description' => 'Layout verification content.',
        ]);

        GuestbookEntry::factory()->create(['post_id' => $post->id]);

        Livewire::test(Feed::class)
            ->assertSee('Guestbook Feed')
            ->assertSee('aria-label="Guestbook feed filters"', false)
            ->assertSee('columns-1', false)
            ->assertSee('md:columns-2', false)
            ->assertSee('xl:columns-3', false)
            ->assertSee('2xl:columns-4', false)
            ->assertSee('break-inside-avoid', false)
            ->assertSee('Write an entry')
            ->set('search', 'Layout')
            ->set('sort', 'oldest')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('sort', 'latest');
    }
}
