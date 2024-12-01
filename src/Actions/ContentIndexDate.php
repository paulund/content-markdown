<?php

namespace Paulund\ContentMarkdown\Actions;

use Carbon\Carbon;
use Paulund\ContentMarkdown\Models\ContentLastIndexed;

class ContentIndexDate
{
    public function execute(): Carbon
    {
        return Carbon::parse(ContentLastIndexed::first()->last_indexed ?? 0);
    }
}
