# Staging site tools

## Installation

Install the package via [Composer](https://getcomposer.org/):

```bash
composer require studio24/staging-site
```

If your application does not already use Composer, make sure you load the vendor packages early in your code (update to use the correct path):

```php
require __DIR__ . '/../vendor/autoload.php';
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

If you want to customise the options, then you need a bit more code. The following examples are for WordPress.

```php
use Studio24\StagingSite\StagingSite;

// Setup staging
$staging = StagingSite::getInstance();
$staging->setStagingEnvironments('staging');
$staging->setEnvironment($_ENV['WP_ENVIRONMENT_TYPE']);

// Add custom setup here...

// Authenticate
if ($staging->isStaging()) {
    $staging->authenticate();
}
```

### How to customise the login page

If you want to customise any options, you can do so via the controller object. Make sure you add your code before `$staging->authenticate()`
is run. 

You can customise any text on the login page via `$staging->loginPage->setPlaceholder($name, $value)`.

Customise the title:

```php
$staging->loginPage->setPlaceholder('title', 'Login to My Website');
```

Customise the footer text (you can include HTML):

```php
$staging->loginPage->setPlaceholder('footer', 'Get support from <a href="mailto:support@studio24.net">Studio 24</a>');
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

### Cookie lifetime

By default, the login cookie is set to expire after 7 days. You can customise this via:

```php
$staging->auth->setCookieLifetime(3600); 
```

This sets the cookie lifetime in seconds (3600 = 1 hour).

You can also set this in days:

```php
$staging->auth->setCookieLifetimeInDays(14); 
```

You can also change the cookie name (default is `staging_site_remember_login`):

```php
$staging->auth->setCookieName('remember_me'); 
``` 
