# Local Development Environment

When developing a Laravel package you'll need a local development environment to test your package.

On my local machine I have a folder for packages `/packages` and a folder for laravel projects `/projects`.

The packages folder is where you would clone this repository into to work on common functionality to your laravel applications.

To use this in a local laravel application you need to add this packages folder as a repository in the `composer.json` file.

```json
{
  "scripts": { ... },

  "repositories": [
    {
      "type": "path",
      "url": "../packages/*"
    }
  ]
}
```

In the laravel application you can now run the following command to install the package.

```bash
composer require vendor-name/package-name
```

This will symlink the package from the packages folder into the vendor folder of the laravel application.

Now any changes you make to the package in the packages folder will be reflected in the laravel application.

## Testing

To test the package you can run the following command.

```bash
vendor/bin/testbench workbench:install
composer test
```

This uses a package called Orchestra testbench which is a Laravel package that is used to test Laravel packages.

The `workbench:install` command will create a new Laravel application in the `workbench` folder and install the package into this application.

The `composer test` command will run the tests in the package.
