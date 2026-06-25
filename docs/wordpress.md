# WordPress

This will display the staging site password based on:

* Environment variable: `WP_ENVIRONMENT_TYPE`
* Staging environments: `staging`

This uses a [must-use plugin](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/) in WordPress, so this cannot be disabled via WordPress admin.

## Composer autoload

If your WordPress installation is not already using Composer, edit your `wp-config.php` file to autoload Composer packages:

```php
// Composer
require_once __DIR__ . '/../vendor/autoload.php';
```

> [!NOTE]  
> Please note you may need to change the path to your vendor directory based on your WordPress installation.

## WP_ENVIRONMENT_TYPE

It is a pre-requisite that the environment variable `WP_ENVIRONMENT_TYPE` is set correctly in your different environments.
This is the default way for WordPress to determine the environment.

See https://developer.wordpress.org/reference/functions/wp_get_environment_type/ 

## Staging site password

It is recommended to not store the password hash in version control. 

### .env

If you are using an `.env` file with your WordPress installation add the password hash to this file:

```
STAGING_SITE_PASSWORD="your password hash"
```

### Set environment variable on your hosting platform

If your hosting platform uses different methods to set environment variables, consult your hosting platform documentation
on how to set the environment variable `STAGING_SITE_PASSWORD`.


### Studio 24 multi-environment config

If you use [studio24/wordpress-multi-env-config](https://github.com/studio24/wordpress-multi-env-config) to help manage 
WordPress config you can add the following to `config/wp-config.local.php` (not in version control): 

```php
define('STAGING_SITE_PASSWORD', 'your password hash');
```

### wp-config.php

As a last resort, you can set the environment variable in your `wp-config.php` file. This is normally committed to version control, so it is not recommended. But given the low risk you may consider this acceptable.

```php
define('STAGING_SITE_PASSWORD', 'your password hash');
```

Replace `your password hash` with the password hash string generated in the "create a password hash" step.

## Must-use plugin

Copy the file [staging-site-password.php](wordpress/staging-site-password.php) to your mu-plugins directory. This ensures the plugin always runs and cannot be removed.

```bash
cp vendor/studio24/staging-site/wordpress/staging-site.php web/content/mu-plugins/
```

> [!NOTE]  
> Please note you may need to change the path to your mu-plugins directory based on your WordPress installation. Common paths are:
> - wp-content/mu-plugins
> - web/wp-content/mu-plugins
> - web/app/mu-plugins
