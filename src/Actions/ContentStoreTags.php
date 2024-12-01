<?php

namespace Paulund\ContentMarkdown\Actions;

use Illuminate\Support\Arr;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Paulund\ContentMarkdown\Models\Content;

class ContentStoreTags
{
    public function execute(Content $content, RenderedContentWithFrontMatter $markdown): Content
    {
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

        return $content;
    }
}
