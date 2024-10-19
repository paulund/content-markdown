<?php

namespace Paulund\ContentMarkdown\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;

    protected function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
            \Paulund\ContentMarkdown\ContentMarkdownServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(
            $app['config']['app.cipher'] === 'AES-128-CBC' ? 16 : 32
        )));
    }
}
