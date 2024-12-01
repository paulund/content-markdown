<?php

namespace Paulund\ContentMarkdown\Actions;

use Paulund\ContentMarkdown\Models\ContentLastIndexed;

class ContentIndexSetNow
{
    public function execute(): void
    {
        ContentLastIndexed::create(['last_indexed' => now()]);
    }
}
