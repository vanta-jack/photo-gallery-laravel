<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoCommentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_photo_comment(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('photos.comments.store', $photo), [
                'body' => 'Amazing composition.',
            ]);

        $response->assertRedirect(route('photos.show', $photo));
        $this->assertDatabaseHas('photo_comments', [
            'photo_id' => $photo->id,
            'user_id' => $user->id,
            'body' => 'Amazing composition.',
        ]);
    }

    public function test_comment_owner_can_update_photo_comment_body(): void
    {
        $user = User::factory()->user()->create();
        $photo = Photo::factory()->create();
        $comment = PhotoComment::factory()->for($photo)->for($user, 'user')->create([
            'body' => 'Initial comment.',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('photos.comments.update', [$photo, $comment]), [
                'body' => 'Updated comment body.',
            ]);

        $response->assertRedirect(route('photos.show', $photo));
        $this->assertDatabaseHas('photo_comments', [
            'id' => $comment->id,
            'body' => 'Updated comment body.',
        ]);
    }
}
