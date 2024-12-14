<?php

namespace Paulund\ContentMarkdown\Actions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Paulund\ContentMarkdown\Models\Content;

class ContentStore
{
    public function execute(string $file, RenderedContentWithFrontMatter $markdown): Content
    {
        $fileParts = explode('/', $file);
        $folder = array_shift($fileParts);
        $filename = array_pop($fileParts);

        $published = $markdown->getFrontMatter()['published'] ?? true;
        $slug = $markdown->getFrontMatter()['slug'] ?? Str::slug($markdown->getFrontMatter()['title']);
        $content = Content::withoutGlobalScopes()->firstOrNew(['slug' => $slug]);

        // Check if the file is a draft
        if (Str::startsWith($filename, config('content-markdown.drafts.prefix', '.'))) {
            $published = false;
        }

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

        return $content;
    }
}
