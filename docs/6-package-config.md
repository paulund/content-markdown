# Package Configs

Configs are a great way to allow users of your package to customize the behavior of your package.

The default config files is stored in the `config` folder of your package.

You can publish new config files to the Laravel application by defining them in the service provider.

```php
$this->publishes([
    __DIR__.'/../config' => config_path(),
], 'vendor/package-name');
```

When you run the command `php artisan vendor:publish --tag=package-name-config` the config file will be copied to the
`config` folder of the Laravel application.

If this changes the user will need to update this config file to reflect the changes.

An alternative approach is to merge the package config into the application config by using the method
`$this->mergeConfigFrom(__DIR__.'/../config/package-name.php', 'package-name');` in the `register` method of the service
provider.
