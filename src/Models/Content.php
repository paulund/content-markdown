<?php

namespace Paulund\ContentMarkdown\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Paulund\ContentMarkdown\Database\Factories\ContentFactory;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;

/**
 * @property string $folder
 * @property string $filename
 * @property string $slug
 * @property bool $published
 * @property ?\Carbon\Carbon $published_at
 * @property Collection $tags
 */
class Content extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'folder',
        'filename',
        'slug',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $dates = [
        'published_at',
    ];

    protected $with = [
        'tags',
    ];

    private bool $attemptPopulateFrontMatter = false;

    private string $frontMatterTitle = '';

    private string $frontMatterDescription = '';

    private string $frontMatterContent = '';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->connection = config('content-markdown.database.connection');
        $this->table = config('content-markdown.database.content_table_name');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('published', true);
        });
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, config('content-markdown.database.content_tags_table_name'));
    }

    /**
     * Scopes
     */
    public function scopeFolder(Builder $query, string $folder): Builder
    {
        return $query->where('folder', $folder);
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeHasTag(Builder $query, string $tag): Builder
    {
        return $query->whereHas('tags', function (Builder $query) use ($tag) {
            $query->where('name', $tag);
        });
    }

    public function getTitleAttribute(): string
    {
        if (empty($this->frontMatterTitle)) {
            $this->populateFrontMatter();
        }

        return $this->frontMatterTitle ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        if (empty($this->frontMatterDescription)) {
            $this->populateFrontMatter();
        }

        return $this->frontMatterDescription ?? '';
    }

    public function getDateAttribute(): ?\Carbon\Carbon
    {
        return $this->published_at;
    }

    public function getContentAttribute(): string
    {
        return $this->frontMatterContent ?? '';
    }

    /**
     * Helpers
     */
    public function populate(): self
    {
        $config = config('content-markdown.commonmark.config');

        $environment = (new Environment($config))
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new FrontMatterExtension)
            ->addExtension(new AutolinkExtension)
            ->addExtension(new HeadingPermalinkExtension)
            ->addExtension(new HighlightCodeExtension(theme: 'github-light'));

        $storage = Storage::disk(config('content-markdown.filesystem.disk', 'local'));
        $markdownConverter = new MarkdownConverter($environment);
        $renderedContent = $markdownConverter->convert($storage->get($this->filename));

        if ($renderedContent instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $renderedContent->getFrontMatter();
            $this->frontMatterTitle = $frontMatter['title'] ?? '';
            $this->frontMatterDescription = $frontMatter['description'] ?? '';
            $this->frontMatterContent = $renderedContent->getContent();
        }

        return $this;
    }

    public function populateFrontMatter(): self
    {
        if ($this->attemptPopulateFrontMatter) {
            return $this;
        }

        $this->attemptPopulateFrontMatter = true;
        $config = config('content-markdown.commonmark.config');

        $environment = (new Environment($config))
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new FrontMatterExtension);

        $storage = Storage::disk(config('content-markdown.filesystem.disk', 'local'));
        $markdownConverter = new MarkdownConverter($environment);
        $renderedContent = $markdownConverter->convert($storage->get($this->filename));

        if ($renderedContent instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $renderedContent->getFrontMatter();
            $this->frontMatterTitle = $frontMatter['title'] ?? '';
            $this->frontMatterDescription = $frontMatter['description'] ?? '';
        }

        return $this;
    }

    public function formatDate($format = 'F j, Y'): string
    {
        return $this->published_at->format($format);
    }

    protected static function newFactory()
    {
        return ContentFactory::new();
    }
}
