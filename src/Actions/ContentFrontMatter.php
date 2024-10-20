<?php

namespace Paulund\ContentMarkdown\Actions;

use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

class ContentFrontMatter
{
    public function frontMatter(string $content): array
    {
        try {
            $parser = (new FrontMatterExtension)->getFrontMatterParser();
            $frontMatter = $parser->parse($content)->getFrontMatter();
        } catch (InvalidFrontMatterException $e) {
            return [];
        }

        if (is_null($frontMatter)) {
            return [];
        }

        return $frontMatter;
    }
}
