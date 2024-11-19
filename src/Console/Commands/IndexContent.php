<?php

namespace Paulund\ContentMarkdown\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Paulund\ContentMarkdown\Actions\ContentMarkdown;
use Paulund\ContentMarkdown\Actions\StorageDisk;
use Paulund\ContentMarkdown\Models\Content;
use Paulund\ContentMarkdown\Models\ContentLastIndexed;

/**
 * This will loop through all the content markdown files in the content folder and index them into the database
 */
class IndexContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:index {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the content markdown files into the database';

    public function __construct(
        private readonly StorageDisk $storageDisk,
        private readonly ContentMarkdown $contentMarkdown
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('force')) {
            ContentLastIndexed::truncate();
        }

        // Get all the markdown files in the content folder
        $files = $this->storageDisk->allFiles();
        $lastIndex = ContentLastIndexed::first()->last_indexed ?? 0;

        foreach ($files as $file) {

            $lastModified = $this->storageDisk->lastModified($file);
            if ($lastModified < $lastIndex) {
                continue;
            }

            $this->info("Processing $file");

            $content = $this->storageDisk->get($file);
            $markdown = $this->contentMarkdown->parse($content);

            if ($markdown instanceof RenderedContentWithFrontMatter) {
                $published = $markdown->getFrontMatter()['published'] ?? true;
                $fileParts = explode('/', $file);
                $folder = array_shift($fileParts);
                $filename = array_pop($fileParts);

                // Check if the file is a draft
                if (Str::startsWith($filename, config('content-markdown.drafts.prefix', '.'))) {
                    $published = false;
                }

                // Save the content to the database
                $content = Content::withoutGlobalScopes()->firstOrNew(['slug' => $markdown->getFrontMatter()['slug']]);

                if ($published) {
                    $content->published_at = isset($markdown->getFrontMatter()['createdAt']) ? Carbon::parse($markdown->getFrontMatter()['createdAt']) : now();
                } elseif (! $published) {
                    $content->published_at = null;
                }

                $content->fill([
                    'title' => $markdown->getFrontMatter()['title'] ?? '',
                    'description' => $markdown->getFrontMatter()['description'] ?? '',
                    'content' => $markdown->getContent(),
                    'folder' => $folder,
                    'filename' => $file,
                    'published' => $published,
                ]);

                $content->save();

                // Tags
                if (isset($markdown->getFrontMatter()['tags'])) {
                    $currentTags = $content->tags->pluck('name')->map('strtolower')->toArray();

                    $allTags = Arr::wrap($markdown->getFrontMatter()['tags']);
                    foreach ($allTags as $tag) {
                        $content->tags()->firstOrCreate(['name' => strtolower($tag)]);
                    }

                    $tagsToDelete = array_diff($currentTags, $allTags);

                    foreach ($tagsToDelete as $tagDelete) {
                        $tagModel = $content->tags()->where('name', strtolower($tagDelete))->first();
                        if ($tagModel) {
                            $content->tags()->detach($tagModel);
                        }
                    }
                }
            }
        }

        // Delete content if the file doesn't exist
        Content::get()->each(function ($content) use ($files) {
            if (! in_array($content->filename, $files)) {
                $content->delete();
            }
        });

        ContentLastIndexed::truncate();
        ContentLastIndexed::create(['last_indexed' => now()->timestamp]);
    }
}
