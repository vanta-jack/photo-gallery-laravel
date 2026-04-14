<?php

namespace Tests\Feature;

use App\Livewire\Albums\PhotoSelector;
use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlbumPhotoSelectorTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_photo_selector_component_renders_on_create_form(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('albums.create'))
            ->assertOk()
            ->assertSee('Cover photo', false)
            ->assertSee('cover_photo_id', false);
    }

    public function test_photo_selector_component_renders_on_edit_form(): void
    {
        $user = User::factory()->user()->create();
        $album = Album::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('albums.edit', $album))
            ->assertOk()
            ->assertSee('Cover photo', false)
            ->assertSee('cover_photo_id', false);
    }

    public function test_photo_selector_component_renders_with_photos(): void
    {
        Livewire::test(PhotoSelector::class, [
            'name' => 'cover_photo_id',
            'label' => 'Cover photo',
            'photos' => Photo::factory()->count(3)->create(),
            'selected' => null,
            'help' => 'Select a cover',
        ])
            ->assertSee('grid grid-cols-2', false)
            ->assertSee('name="cover_photo_id"', false);
    }

    public function test_photo_selector_component_renders_empty_state(): void
    {
        Livewire::test(PhotoSelector::class, [
            'name' => 'cover_photo_id',
            'label' => 'Cover photo',
            'photos' => [],
            'selected' => null,
        ])
            ->assertSee('No photos available yet.', false);
    }

    public function test_photo_selector_can_clear_selection(): void
    {
        $photos = Photo::factory()->count(3)->create();

        Livewire::test(PhotoSelector::class, [
            'name' => 'cover_photo_id',
            'label' => 'Cover photo',
            'photos' => $photos,
            'selected' => $photos->first()->id,
        ])
            ->call('clearSelection')
            ->assertSet('selected', null);
    }

    public function test_photo_selector_updates_selection_via_wire_model(): void
    {
        $photos = Photo::factory()->count(3)->create();

        Livewire::test(PhotoSelector::class, [
            'name' => 'cover_photo_id',
            'label' => 'Cover photo',
            'photos' => $photos,
            'selected' => null,
        ])
            ->set('selected', $photos->first()->id)
            ->assertSet('selected', $photos->first()->id);
    }

    public function test_album_create_with_cover_photo_selector(): void
    {
        $user = User::factory()->user()->create();
        $photos = Photo::factory()->for($user)->count(3)->create();

        $this->actingAs($user)
            ->post(route('albums.store'), [
                'title' => 'Summer Collection',
                'description' => 'Great summer memories',
                'cover_photo_id' => $photos->first()->id,
                'photo_ids' => [$photos->first()->id],
                'is_private' => false,
            ])
            ->assertRedirect(route('albums.show', Album::first()));

        $album = Album::first();
        $this->assertEquals($photos->first()->id, $album->cover_photo_id);
    }

    public function test_album_edit_with_cover_photo_selector(): void
    {
        $user = User::factory()->user()->create();
        $album = Album::factory()->for($user)->create();
        $photos = Photo::factory()->for($user)->count(3)->create();
        $album->photos()->sync($photos->pluck('id'));

        $newCover = $photos->last();

        $this->actingAs($user)
            ->put(route('albums.update', $album), [
                'title' => 'Updated Title',
                'description' => 'Updated description',
                'cover_photo_id' => $newCover->id,
                'photo_ids' => $album->photos->pluck('id')->toArray(),
                'is_private' => false,
            ])
            ->assertRedirect(route('albums.show', $album));

        $album->refresh();
        $this->assertEquals($newCover->id, $album->cover_photo_id);
    }
}
