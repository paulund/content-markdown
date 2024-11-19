<?php

namespace Paulund\ContentMarkdown\Actions;

use Illuminate\Support\Facades\Storage;

class StorageDisk
{
    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('content-markdown.filesystem.disk'));
    }

    /**
     * @return string[]
     */
    public function folders(?string $directory = null): array
    {
        return $this->disk()->allDirectories($directory);
    }

    /**
     * @return string[]
     */
    public function allFiles(?string $directory = null): array
    {
        return $this->disk()->allFiles($directory);
    }

    public function get(string $file): string
    {
        return $this->disk()->get($file);
    }

    public function exists(string $file): bool
    {
        return $this->disk()->exists($file);
    }
}
