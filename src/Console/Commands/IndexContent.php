<?php

namespace Paulund\ContentMarkdown\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use Paulund\ContentMarkdown\Models\Content;

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
    protected $signature = 'content:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the content markdown files into the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Get all the markdown files in the content folder
        $files = $this->storageDisk()->allFiles();

        foreach ($files as $file) {
            $this->info("Processing $file");

            // Get the frontmatter of the file
            $environment = (new Environment([]))
                ->addExtension(new CommonMarkCoreExtension)
                ->addExtension(new FrontMatterExtension);
            $markdownConverter = new MarkdownConverter($environment);
            $renderedContent = $markdownConverter->convert($this->storageDisk()->get($file));

            if ($renderedContent instanceof RenderedContentWithFrontMatter) {
                $frontMatter = $renderedContent->getFrontMatter();

                $published = $frontMatter['published'] ?? true;
                $fileParts = explode('/', $file);
                $folder = array_shift($fileParts);
                $filename = array_pop($fileParts);

                // Check if the file is a draft
                if (Str::startsWith($filename, config('content-markdown.drafts.prefix', '.'))) {
                    $published = false;
                }

                // Save the content to the database
                $content = Content::withoutGlobalScopes()->firstOrNew(['slug' => $frontMatter['slug']]);

                if ($published) {
                    $content->published_at = isset($frontMatter['createdAt']) ? Carbon::parse($frontMatter['createdAt']) : now();
                } elseif (! $published) {
                    $content->published_at = null;
                }

                $content->fill([
                    'folder' => $folder,
                    'filename' => $file,
                    'published' => $published,
                ]);

                $content->save();

                // Tags
                if (isset($frontMatter['tags'])) {
                    $currentTags = $content->tags->pluck('name')->map('strtolower')->toArray();

                    foreach ($frontMatter['tags'] as $tag) {
                        $content->tags()->firstOrCreate(['name' => strtolower($tag)]);
                    }

                    $tagsToDelete = array_diff($currentTags, $frontMatter['tags']);

                    foreach ($tagsToDelete as $tagDelete) {
                        $tagModel = $content->tags()->where('name', strtolower($tagDelete))->first();
                        if ($tagModel) {
                            $content->tags()->detach($tagModel);
                        }
                    }
                }
            }
        }
    }

    private function storageDisk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('content-markdown.filesystem.disk', 'local'));
    }
}
