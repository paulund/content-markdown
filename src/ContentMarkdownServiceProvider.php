<?php

namespace Paulund\ContentMarkdown;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Paulund\ContentMarkdown\Console\Commands\IndexContent;

class ContentMarkdownServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'paulund/content-markdown');

        if ($this->app->runningInConsole()) {
            $this->commands([
                IndexContent::class,
            ]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(IndexContent::class)->daily();
        });
    }
}
