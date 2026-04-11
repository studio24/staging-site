#  Staging Site tools

[![Latest Version on Packagist](https://img.shields.io/packagist/v/studio24/staging-site.svg?style=flat-square)](https://packagist.org/packages/studio24/staging-site)
[![Tests](https://img.shields.io/github/actions/workflow/status/studio24/staging-site/php.yml?branch=main&label=tests&style=flat-square)](https://github.com/studio24/staging-site/actions/workflows/php.yml)

A set of simple tools to help manage staging websites, designed for PHP-powered websites.

## What does it do?

When creating or updating a website it's a good idea to publish changes to a staging environment to review before going live.

This package adds the following simple tools to a staging site:

* Secure staging websites behind a simple, accessible login page
* Informs search engine spiders to not index a staging website
* Displays a banner indicating the site is a staging site

It has support for WordPress, Craft CMS, Laravel, Symfony and plain PHP.

### Staging site login

The staging site login page is a replacement for basic authentication, which doesn't play well with password managers and is blocked by some client systems.

* It detects the current environment and if it matches staging, it displays the staging site login page
* The staging site login page asks for a single password to access the site
* Once the correct password is entered, a cookie is set to remember the login for a set time period (7 days by default)

[![Screenshot of the staging site password login page](screenshot.png)](screenshot.png)

### Staging banner

TODO

## Requirements

* PHP 7.4+

## Installation

See the [documentation](docs/README.md) for instructions on how to install this on your website.

## Testing

## Testing

```shell
php -S localhost:8000 -t tests/website
```

Access http://localhost:8000

OR?

```shell
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Find out more about [how to contribute](CONTRIBUTING.md) and our [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Issues

If you discover a security vulnerability within this package, please follow our [disclosure procedure](SECURITY.md).

## About

This package is developed by [Studio 24](https://www.studio24.net/), a human-centered digital agency who build websites and web apps that work for everyone.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
