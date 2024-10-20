<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Paulund\ContentMarkdown\Actions\StorageDisk;

beforeEach(function () {
    Storage::fake('content-markdown');
    Config::set('content-markdown.filesystem.disk', 'content-markdown');
    $this->storageDisk = new StorageDisk;
});

it('retrieves all files from the disk', function () {
    Storage::disk('content-markdown')->put('file1.md', 'Content of file 1');
    Storage::disk('content-markdown')->put('file2.md', 'Content of file 2');

    $files = $this->storageDisk->allFiles();

    expect($files)->toBeArray();
    expect($files)->toContain('file1.md');
    expect($files)->toContain('file2.md');
});

it('retrieves the content of a file', function () {
    Storage::disk('content-markdown')->put('file.md', 'File content');

    $content = $this->storageDisk->get('file.md');

    expect($content)->toBe('File content');
});

it('checks if a file exists', function () {
    Storage::disk('content-markdown')->put('file.md', 'File content');

    $exists = $this->storageDisk->exists('file.md');

    expect($exists)->toBeTrue();
});

it('returns false if a file does not exist', function () {
    $exists = $this->storageDisk->exists('nonexistent.md');

    expect($exists)->toBeFalse();
});
