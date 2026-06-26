# Plain PHP integration

## Staging site password

You can store the password hash in the following ways.

### Environment variable

You can store the hashed staging site password as an environment variable named `STAGING_SITE_PASSWORD` so it's not in your source code.

For example, in an `.env` file (not commited to source control):

```dotenv
STAGING_SITE_PASSWORD="your password hash"
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

### PHP constant

If this isn't possible, you can also pass the password hash as a PHP constant in your PHP code:

```php
define('STAGING_SITE_PASSWORD', 'your password hash');
```

## Add to your website

Add the following code near the entry point (the start) of your website application, this is often the `index.php` file in your document root.

If your application does not already use Composer, add this code to autoload Composer packages:

```php
// Composer
require_once __DIR__ . '/../vendor/autoload.php';
```

> [!NOTE]  
> Please note you may need to change the path to your vendor directory based on your project folder structure.


Add the following lines:

```php
// Display login page on staging
\Studio24\StagingSite\StagingSite::run('ENVIRONMENT', 'staging');
```

This depends on the environment being set either as a PHP constant or an environment variable. If this isn't the case, 
see the [Auto-prepend file](#auto-prepend-file) section below.

This will display the staging site password based on:

* Environment variable: `ENVIRONMENT`
* Staging environments: `staging`

### Auto-prepend file

If you don't have access to a current environment in your PHP files and you just want to automatically protect all PHP requests, you can use [auto-prepend file](https://www.php.net/manual/en/ini.core.php#ini.auto-prepend-file). 

First, create a file to automatically load before all PHP requests, for example `staging.php`

Add the code:

```php
require_once __DIR__ . '/vendor/autoload.php';

use Studio24\StagingSite\StagingSite;

// Setup staging
$staging = StagingSite::getInstance();
$staging->setStagingEnvironments('staging');
$staging->setEnvironment($_ENV['ENVIRONMENT']);

// Authenticate
if ($staging->isStaging()) {
    $staging->authenticate();
}
```

Make sure you set the correct vendor path and replace `$_ENV['ENVIRONMENT']` with the correct code to retrieve the current environment.
This is easier to do if environment variables are set by your hosting platform.

If you have an `.env` file with the current environment (not in version control) you can use this code to load this. 
We've included two examples using popular dotenv packages.

Using [symfony/dotenv](https://github.com/symfony/dotenv):


```php
require_once __DIR__ . '/vendor/autoload.php';

use Studio24\StagingSite\StagingSite;
use Symfony\Component\Dotenv\Dotenv;

// Load .env
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Setup staging
$staging = StagingSite::getInstance();
$staging->setStagingEnvironments('staging');
$staging->setEnvironment($_ENV['ENVIRONMENT']);

// Authenticate
if ($staging->isStaging()) {
    $staging->authenticate();
}
```

Using [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv):

```php
require_once __DIR__ . '/vendor/autoload.php';

use Studio24\StagingSite\StagingSite;

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Setup staging
$staging = StagingSite::getInstance();
$staging->setStagingEnvironments('staging');
$staging->setEnvironment($_ENV['ENVIRONMENT']);

// Authenticate
if ($staging->isStaging()) {
    $staging->authenticate();
}
```

You can auto-prepend this file to all PHP requests in a folder by adding this to your `.htaccess` file:

```shell
php_value auto_prepend_file /path/to/staging.php
```

Make sure the path to `staging.php` is correct to the htaccess file. Please note this uses PHP's `include_path` so the path can be relative to the include path.


