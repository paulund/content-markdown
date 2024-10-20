<?php

use Paulund\ContentMarkdown\Actions\ContentFrontMatter;

it('parses valid front matter', function () {
    $content = <<<'EOT'
---
title: "Test Title"
description: "Test Description"
---
# Content
EOT;

    $contentFrontMatter = new ContentFrontMatter;
    $result = $contentFrontMatter->frontMatter($content);

    expect($result)->toBeArray();
    expect($result)->toHaveKey('title', 'Test Title');
    expect($result)->toHaveKey('description', 'Test Description');
});

it('returns empty array for invalid front matter', function () {
    $content = <<<'EOT'
---
title: "Test Title"
description: "Test Description"
# Content
EOT;

    $contentFrontMatter = new ContentFrontMatter;
    $result = $contentFrontMatter->frontMatter($content);

    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('returns empty array for content without front matter', function () {
    $content = <<<'EOT'
# Content
EOT;

    $contentFrontMatter = new ContentFrontMatter;
    $result = $contentFrontMatter->frontMatter($content);

    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});
