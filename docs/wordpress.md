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

## Staging site password
If you are using an `.env` file with your WordPress installation add the password hash to this file:

```
STAGING_SITE_PASSWORD="your password hash"
```

If you are not using an `.env` file, edit your `wp-config.php` file to add the password hash:

```php
define('STAGING_SITE_PASSWORD', 'your password hash');
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

## Must-use plugin

Copy the file [staging-site-password.php](wordpress/staging-site-password.php) to your mu-plugins directory:

```bash
cp vendor/studio24/staging-site/wordpress/staging-site.php wp-content/mu-plugins/
```

> [!NOTE]  
> Please note you may need to change the path to your mu-plugins directory based on your WordPress installation.
