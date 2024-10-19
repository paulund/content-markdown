<?php

namespace Paulund\ContentMarkdown\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Paulund\ContentMarkdown\Models\Content;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Paulund\ContentMarkdown\Models\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'folder' => 'blog',
            'filename' => $this->faker->slug,
            'slug' => $this->faker->slug,
            'published' => true,
            'published_at' => now(),
        ];
    }
}
