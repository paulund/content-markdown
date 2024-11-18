<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Paulund\ContentMarkdown\Models\Content;

uses(RefreshDatabase::class);

it('will index content markdown files into the database', function () {
    Storage::fake('content');
    Config::set('content-markdown.filesystem.disk', 'content');

    Storage::disk('content')->put('content/2021-01-01-test.md', '---
title: "Test Title"
slug: "test-title"
description: "Test Description"
---

Content here');

    Storage::disk('content')->put('content/2021-01-02-test.md', '---
title: "Test Title 2"
slug: "test-title-2"
description: "Test Description 2"
---

Content here');

    $this->artisan('content:index')
        ->expectsOutput('Processing content/2021-01-01-test.md')
        ->expectsOutput('Processing content/2021-01-02-test.md')
        ->assertExitCode(0);

    $this->assertDatabaseHas('contents', [
        'slug' => 'test-title',
        'filename' => 'content/2021-01-01-test.md',
    ]);
});

it('will delete content if file doesnt exist', function () {
    Storage::fake('content');
    Config::set('content-markdown.filesystem.disk', 'content');

    Storage::disk('content')->put('content/2021-01-01-test.md', '---
title: "Test Title"
slug: "test-title"
description: "Test Description"
---

Content here');

    Content::factory()->create([
        'slug' => 'test-title',
        'filename' => 'content/2021-01-02-test.md',
    ]);

    $this->assertDatabaseHas('contents', [
        'slug' => 'test-title',
        'filename' => 'content/2021-01-02-test.md',
    ]);

    $this->artisan('content:index')
        ->expectsOutput('Processing content/2021-01-01-test.md')
        ->assertExitCode(0);

    $this->assertDatabaseHas('contents', [
        'slug' => 'test-title',
        'filename' => 'content/2021-01-01-test.md',
    ]);

    $this->assertDatabaseMissing('contents', [
        'slug' => 'test-title',
        'filename' => 'content/2021-01-02-test.md',
    ]);
});
