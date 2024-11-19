<?php

namespace Paulund\ContentMarkdown\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Paulund\ContentMarkdown\Database\Factories\ContentFactory;

/**
 * @property string $title
 * @property string $descritpion
 * @property string $content
 * @property string $folder
 * @property string $filename
 * @property string $slug
 * @property bool $published
 * @property ?\Carbon\Carbon $published_at
 * @property Collection $tags
 */
class Content extends Model
{
    /** @use HasFactory<ContentFactory> */
    use HasFactory;

    use HasTimestamps;

    protected $fillable = [
        'title',
        'description',
        'content',
        'folder',
        'filename',
        'slug',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * @var array|string[]
     */
    protected array $dates = [
        'published_at',
    ];

    protected $with = [
        'tags',
    ];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('content-markdown.database.connection');
        $this->table = config('content-markdown.database.content_table_name');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('published', true);
        });
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, config('content-markdown.database.content_tags_table_name'));
    }

    /**
     * Scopes
     */

    /**
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeFolder(Builder $query, string $folder): Builder
    {
        return $query->where('folder', $folder);
    }

    /**
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * @param  Builder<Content>  $query
     * @return Builder<Content>
     */
    public function scopeHasTag(Builder $query, string $tag): Builder
    {
        return $query->whereHas('tags', function (Builder $query) use ($tag) {
            $query->where('name', $tag);
        });
    }

    public function getDateAttribute(): ?\Carbon\Carbon
    {
        return $this->published_at;
    }

    /**
     * Helpers
     */
    public function formatDate(string $format = 'F j, Y'): string
    {
        return $this->published_at->format($format);
    }

    protected static function newFactory(): ContentFactory
    {
        return ContentFactory::new();
    }
}
