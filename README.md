# :package-name:

[![Latest Version on Packagist](https://img.shields.io/packagist/v/:vendor:/:package-name:.svg?style=flat-square)](https://packagist.org/packages/:vendor:/:package-name:)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/:vendor:/:package-name:/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/:vendor:/:package-name:/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/:vendor:/:package-name:/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/:vendor:/:package-name:/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/:vendor:/:package-name:.svg?style=flat-square)](https://packagist.org/packages/:vendor:/:package-name:)

---
A repository to use for creating Laravel packages.

## Template Usage
Press the "Use this template" button at the top of this repo to create a new repo with the contents of this skeleton.

When you clone this repo there are templated strings that will need to be replaced. You can take the following and
search and replace them with the appropriate values.

- `:vendor:` - The vendor name of the package. This is the name of the person or company that is creating the package.
- `:package-name:` - The name of the package. This is the name of the package that you are creating.
- `:package-description:` - The description of the package. This is a short description of what the package does.
- `VendorName` - The name of the vendor used for namespace.
- `PackageName` - The name of the package used for namespace.
- `:github-profile:` - The name of the GitHub profile that the package is associated with.

## Installation
Install the package via composer:

```bash
composer require :vendor:/:package-name:
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="VendorName\PackageName\PackageNameServiceProvider"
php artisan migrate # If the package has migrations
```

## Usage

## Testing
```bash
vendor/bin/testbench workbench:install
composer test
```

## Credits

- [paulund](https://github.com/paulund)
- [All Contributors](contributors.md)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
