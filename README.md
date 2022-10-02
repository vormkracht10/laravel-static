# Serving your Laravel app with speed using static caching

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-static.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-static)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vormkracht10/laravel-static/run-tests?label=tests)](https://github.com/vormkracht10/laravel-static/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vormkracht10/laravel-static/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/vormkracht10/laravel-static/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-static.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-static)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/laravel-static
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-static-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-static-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-static-views"
```

## Usage

```php
$laravelStatic = new Vormkracht10\LaravelStatic();
echo $laravelStatic->echoPhrase('Hello, Vormkracht10!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mark van Eijk](https://github.com/vormkracht10)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
