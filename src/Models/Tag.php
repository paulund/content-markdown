<?php

namespace Paulund\ContentMarkdown\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Paulund\ContentMarkdown\Database\Factories\TagFactory;

/**
 * @property string $slug
 * @property bool $published
 * @property array $frontmatter
 * @property ?\Carbon\Carbon $published_at
 */
class Tag extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'name',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('content-markdown.database.connection');
        $this->table = config('content-markdown.database.tags_table_name');
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class, config('content-markdown.database.content_tags_table_name'));
    }

    protected static function newFactory()
    {
        return TagFactory::new();
    }
}
