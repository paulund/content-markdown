<?php

use League\CommonMark\Output\RenderedContentInterface;
use Paulund\ContentMarkdown\Actions\ContentMarkdown;

beforeEach(function () {
    $this->contentMarkdown = new ContentMarkdown;
});

it('parses valid markdown content', function () {
    $content = <<<'EOT'
# Heading

This is a paragraph with [a link](https://example.com).
EOT;

    $result = $this->contentMarkdown->parse($content);

    expect($result)->toBeInstanceOf(RenderedContentInterface::class);
    expect($result->getContent())->toContain('<h1><a id="content-heading" href="#content-heading" class="heading-permalink" aria-hidden="true" title="Permalink">¶</a>Heading</h1>');
    expect($result->getContent())->toContain('<p>This is a paragraph with <a href="https://example.com">a link</a>.</p>');
});

it('returns null for empty content', function () {
    $content = '';

    $result = $this->contentMarkdown->parse($content);

    expect($result->getContent())->toBeEmpty();
});
