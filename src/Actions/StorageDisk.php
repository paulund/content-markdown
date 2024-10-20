<?php

namespace Paulund\ContentMarkdown\Actions;

use Illuminate\Support\Facades\Storage;

class StorageDisk
{
    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('content-markdown.filesystem.disk'));
    }

    public function allFiles(): array
    {
        return $this->disk()->allFiles();
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
