<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate markdown-formatted content
        $paragraphs = fake()->paragraphs(fake()->numberBetween(2, 5));
        $markdown = '';
        
        foreach ($paragraphs as $i => $paragraph) {
            $markdown .= $paragraph . "\n\n";
            
            // Randomly add markdown formatting
            if (fake()->boolean(30)) {
                $markdown .= "- " . fake()->sentence() . "\n";
                $markdown .= "- " . fake()->sentence() . "\n\n";
            }
        }

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => trim($markdown),
        ];
    }
}
