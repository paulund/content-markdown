# Routing

From within your Laravel package you can add routes to your package by creating a routes file in the `src/Http/Routes/web.php` folder of your package.

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('package', function () {
    return 'Hello from the package';
});
```

You can then load the routes from the package into the Laravel application by adding the following code to the `boot` method of the service provider.

```php
public function boot()
{
    $this->loadRoutesFrom(__DIR__.'/Http/Routes/web.php');
}
```

You can use controllers in your Laravel package by creating a controller in the `src/Http/Controllers` folder of your package
and defining this in the routes file.

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('package', 'VendorName\PackageName\Http\Controllers\PackageController@index');
```

## Views

You can add views to your package by creating a views folder in the `src/resources/views` folder of your package.

You can then load the views from the package into the Laravel application by adding the following code to the `boot` method of the service provider.

```php
public function boot()
{
    $this->loadViewsFrom(__DIR__.'/resources/views', 'package-name');
}
```

## View Components

You can add view components to your package by creating a components folder in the `src/resources/views` folder of your package.

You can then load the view components from the package into the Laravel application by adding the following code to the `boot` method of the service provider.

```php
public function boot()
{
    $this->loadViewComponentsAs('package-name', [
        'component-name' => Component::class,
    ]);
}
```
