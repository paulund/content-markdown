# Assets

Assets are files that are included in the final build of your app. This includes images, fonts, and other files that are not JavaScript or CSS.

Your package may have assets like images, fonts, or other files that you want to include in the final build of your app.

To include assets in your package you can add them to the `resources/assets` folder of your package.

To include these assets in the final build of your app you can use the `publish` command in the service provider.

```php
public function boot()
{
    $this->publishes([
        __DIR__.'/resources/assets' => public_path('vendor/package-name'),
    ], 'package-name-assets');
}
```
