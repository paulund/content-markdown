<?php

namespace Paulund\ContentMarkdown\Actions;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContentInterface;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;

class ContentMarkdown
{
    public function parse(string $content): RenderedContentInterface|RenderedContentWithFrontMatter
    {
        $config = config('content-markdown.commonmark.config', []);
        $environment = (new Environment($config))
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new FrontMatterExtension)
            ->addExtension(new AutolinkExtension)
            ->addExtension(new HeadingPermalinkExtension)
            ->addExtension(new HighlightCodeExtension(theme: 'github-light'));

        return (new MarkdownConverter($environment))->convert($content);
    }
}
