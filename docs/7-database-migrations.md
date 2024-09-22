# Database Migrations

Migrations are a way of running database changes in a structured way. They allow you to create, modify, and delete database tables and columns.

When you create a new Laravel package you can include migrations in the package to allow users to run the migrations when they install the package.

To create a new migration add them into the `database/migrations` folder of your package.

## Publish Migrations

To publish the migrations to the Laravel application you need to add the following code to the `boot` method of the service provider.

```php
public function boot()
{
    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
}
```

This will load the migrations from the package into the Laravel application.

To publish the migrations to the Laravel application you can run the following command.

```bash
php artisan vendor:publish --tag=package-name-migrations
```

This will copy the migrations from the package to the `database/migrations` folder of the Laravel application.

You can then run the migrations from the Laravel application.

```bash
php artisan migrate
```
