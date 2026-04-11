# Staging site tools

## Installation

You can install the package via [Composer](https://getcomposer.org/):

```bash
composer require studio24/staging-site
```

## Create a password hash

You first need to create a staging site password and store this securely. You can generate this via PHP's 
[password_hash](https://www.php.net/password-hash) function or by using the provided command line script:

```bash
php vendor/bin/password-hash.php
```

This will generate a password hash, such as:

```shell
$2y$12$d.GZ1e/QVmM4SMJ4e9/42ehSyVV28.GTQKfqfd2P7WkLDYE519Ela
```

## Platforms

Next choose your platform to install this on your website. In the examples below, replace the password hash string with your actual password hash.

* [WordPress](wordpress.md)
* [Craft CMS](craftcms.md)
* [Laravel](laravel.md)
* [Symfony](symfony.md)
* [Plain PHP integration](php.md)

## Customisation options

TODO

## Customise options

If you want to customise the options, then you need a bit more code:

```php
$controller = new \Studio24\StagingSitePassword\Controller($platform, $passwordHash);
if ($controller->isStaging()) {
    $controller->authenticate();
}
```

## How to customise the login page

If you want to customise any options, you can do so via the controller object. Make sure you add your code before `$controller->authenticate()`
is run. For example:

```php
$controller = new \Studio24\StagingSitePassword\Controller($platform);
$controller->loginPage->setPlaceholder('title', 'Login to My Website');
if ($controller->isStaging()) {
    $controller->authenticate();
}
```

### Text

You can customise any text on the login page via `$controller->loginPage->setPlaceholder($name, $value)`.

Customise the title:

```php
$controller->loginPage->setPlaceholder('title', 'Login to My Website');
```

Customise the footer text (you can include HTML):

```php
$controller->loginPage->setPlaceholder('footer', 'Get support from <a href="mailto:support@studio24.net">Studio 24</a>');
```

The full list of placeholders:

| Placeholder name        | Default value             |
|-------------------------|---------------------------|
| `title`                 | Login to staging website  |
| `footer`                |                           |
| `password_field_label`  | Password                  |
| `submit_field_label`    | Login                     |
| `title_prefix_on_error` | Error:                    |
| `error_message_title`   | There is a problem        |
| `error_message`         | The password is incorrect |
| `show`                  | Show                      |
| `hide`                  | Hide                      |
| `show_password`         | Show password             |
| `hide_password`         | Hide password             |

### Cookie lifetime

By default, the login cookie is set to expire after 7 days. You can customise this via:

```php
$controller->auth->setCookieLifetime(3600); 
```

This sets the cookie lifetime in seconds.

You can also set this in days:

```php
$controller->auth->setCookieLifetimeInDays(14); 
```

You can also change the cookie name (default is `staging_site_remember_login`):

```php
$controller->auth->setCookieName('remember_me'); 
``` 
