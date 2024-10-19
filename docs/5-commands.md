# Commands

To create a artisan command in this package you should add a new folder into `src/Console` and add a new command class
into this folder.

The command class should extend the `Illuminate\Console\Command` class and implement the `handle` method.

When the class has been created you can register the command with your package by adding the command to the `commands`
array in the service provider class.

```php
if ($this->app->runningInConsole()) {
    $this->commands([
        // add commands here
    ]);
}
```

## Command Scheduler

You can add a command to the Laravel scheduler by adding the command in your service provider

```php
$this->app->booted(function () {
    $schedule = $this->app->make(Schedule::class);
    $schedule->command('some:command')->everyMinute();
});
```
