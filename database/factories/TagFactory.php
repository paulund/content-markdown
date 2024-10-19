<?php

namespace Paulund\ContentMarkdown\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Paulund\ContentMarkdown\Models\Tag;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Paulund\ContentMarkdown\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
