<?php

namespace Paulund\ContentMarkdown\Actions;

use Paulund\ContentMarkdown\Models\ContentLastIndexed;

class ContentIndexClear
{
    public function execute(): void
    {
        ContentLastIndexed::truncate();
    }
}
