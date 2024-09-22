# Service Providers

The service provider class is the central place to configure your package. This is where you can register your package's
services into the Laravel service container.

The service provider will have 2 main methods that you can use to register your services.

- `register()` - This method is used to bind your services into the service container.
- `boot()` - This method is used to boot your services after all the services have been registered.

## Register Method

The `register()` method is used to bind your services into the service container. This is where you can bind your services
into the container using the `bind()` method.

```php
public function register()
{
    $this->app->bind(alias, function ($app) {
        return new class();
    });
}
```

In the above example, we are binding a class into the service container using the `bind()` method. The first parameter is the
alias of the class that you want to bind, and the second parameter is a closure that will return a new instance of the class.

## Boot Method

The `boot()` method is used to boot your services after all the services have been registered. This is where you can use
the services that have been registered in the service container.

```php
public function boot()
{
    // application logic
}
```

Within a laravel application you would normally need to define the service provider in the `config/app.php` file.

But in this package we are autoloading the service provider so you don't need to define it in the `config/app.php` file, by adding
the following into the `composer.json` file.

```php
"extra": {
    "laravel": {
        "providers": [
            "VendorName\\PackageName\\PackageNameServiceProvider"
        ]
    }
},
```
