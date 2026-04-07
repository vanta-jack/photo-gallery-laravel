<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Milestone;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MilestonePhotoRequirementTest extends TestCase
{
    use RefreshDatabase;

    private function fakeWebpDataUri(): string
    {
        return 'data:image/webp;base64,' . base64_encode('milestone-photo');
    }

    public function test_milestone_requires_photo_selection_or_upload(): void
    {
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('milestones.store'), [
                'stage' => 'baby',
                'label' => 'First Smile',
                'description' => 'Milestone without photo',
            ]);

        $response->assertSessionHasErrors('photo_id');
        $this->assertDatabaseCount('milestones', 0);
    }

    public function test_milestone_can_use_existing_photo_and_attach_album(): void
    {
        $user = User::factory()->user()->create();
        $album = Album::factory()->for($user)->create();
        $photo = Photo::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->post(route('milestones.store'), [
                'stage' => 'baby',
                'label' => 'First Smile',
                'description' => 'Using existing photo',
                'photo_id' => $photo->id,
                'album_id' => $album->id,
            ]);

        $milestone = Milestone::query()->first();

        $this->assertNotNull($milestone);
        $response->assertRedirect(route('milestones.show', $milestone));
        $this->assertSame($photo->id, $milestone->photo_id);
        $this->assertDatabaseHas('album_photo', [
            'album_id' => $album->id,
            'photo_id' => $photo->id,
        ]);
    }

    public function test_milestone_uploads_multiple_photos_and_uses_first_for_milestone(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('milestones.store'), [
                'stage' => 'grade_school',
                'label' => 'Grade 1',
                'photos' => [
                    $this->fakeWebpDataUri(),
                    $this->fakeWebpDataUri(),
                ],
            ]);

        $milestone = Milestone::query()->first();

        $this->assertNotNull($milestone);
        $response->assertRedirect(route('milestones.show', $milestone));
        $this->assertDatabaseCount('photos', 2);

        $firstPhotoId = Photo::query()->orderBy('id')->value('id');
        $this->assertSame($firstPhotoId, $milestone->photo_id);

        Photo::query()->get()->each(fn (Photo $photo) => Storage::disk('public')->assertExists($photo->path));
    }

    public function test_milestone_allows_custom_stage_when_selected(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->post(route('milestones.store'), [
                'stage' => 'custom',
                'stage_custom' => 'Gap Year',
                'label' => 'Travel Abroad',
                'photo_id' => $photo->id,
            ]);

        $milestone = Milestone::query()->first();

        $this->assertNotNull($milestone);
        $response->assertRedirect(route('milestones.show', $milestone));
        $this->assertSame('Gap Year', $milestone->stage);
    }

    public function test_milestone_requires_custom_stage_value_when_custom_is_selected(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->for($user)->create();

        $response = $this
            ->actingAs($user)
            ->post(route('milestones.store'), [
                'stage' => 'custom',
                'label' => 'Travel Abroad',
                'photo_id' => $photo->id,
            ]);

        $response->assertSessionHasErrors('stage_custom');
        $this->assertDatabaseCount('milestones', 0);
    }

    public function test_milestone_update_allows_setting_custom_stage(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->for($user)->create();
        $milestone = Milestone::factory()->for($user)->create([
            'photo_id' => $photo->id,
            'stage' => 'grade_school',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('milestones.update', $milestone), [
                'stage' => 'custom',
                'stage_custom' => 'Residency',
                'label' => 'Hospital Rotation Start',
            ]);

        $response->assertRedirect(route('milestones.show', $milestone));
        $milestone->refresh();

        $this->assertSame('Residency', $milestone->stage);
        $this->assertSame('Hospital Rotation Start', $milestone->label);
    }
}
