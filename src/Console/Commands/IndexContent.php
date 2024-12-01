<?php

namespace Paulund\ContentMarkdown\Console\Commands;

use Illuminate\Console\Command;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Paulund\ContentMarkdown\Actions\ContentIndexClear;
use Paulund\ContentMarkdown\Actions\ContentIndexDate;
use Paulund\ContentMarkdown\Actions\ContentIndexSetNow;
use Paulund\ContentMarkdown\Actions\ContentMarkdown;
use Paulund\ContentMarkdown\Actions\ContentStore;
use Paulund\ContentMarkdown\Actions\ContentStoreTags;
use Paulund\ContentMarkdown\Actions\StorageDisk;
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
    protected $signature = 'content:index {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the content markdown files into the database';

    public function __construct(
        private readonly StorageDisk $storageDisk,
        private readonly ContentMarkdown $contentMarkdown,
        private readonly ContentIndexClear $contentIndexClear,
        private readonly ContentIndexDate $contentIndexDate,
        private readonly ContentIndexSetNow $contentIndexSetNow,
        private readonly ContentStore $contentStore,
        private readonly ContentStoreTags $contentStoreTags,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('force')) {
            $this->contentIndexClear->execute();
        }

        // Get all the markdown files in the content folder
        $files = $this->storageDisk->allFiles();
        $lastIndex = $this->contentIndexDate->execute();

        foreach ($files as $file) {
            $lastModified = $this->storageDisk->lastModifiedDate($file);
            if ($lastModified < $lastIndex) {
                continue;
            }

            $this->info("Processing $file");

            $markdown = $this->contentMarkdown->parse(
                $this->storageDisk->get($file)
            );

            if ($markdown instanceof RenderedContentWithFrontMatter) {
                $content = $this->contentStore->execute($file, $markdown);
                $this->contentStoreTags->execute($content, $markdown);
            }
        }

        // Delete content if the file doesn't exist
        Content::get()->each(function ($content) use ($files) {
            if (! in_array($content->filename, $files)) {
                $content->delete();
            }
        });

        $this->contentIndexClear->execute();
        $this->contentIndexSetNow->execute();
    }
}
