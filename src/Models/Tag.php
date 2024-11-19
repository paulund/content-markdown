<?php

namespace Paulund\ContentMarkdown\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Paulund\ContentMarkdown\Database\Factories\TagFactory;

/**
 * @property string $name
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'name',
    ];

    /**
     * Tag constructor.
     *
     * @param  array<string,  mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('content-markdown.database.connection');
        $this->table = config('content-markdown.database.tags_table_name');
    }

    /**
     * @return BelongsToMany<Content, $this>
     */
    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, config('content-markdown.database.content_tags_table_name'));
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
