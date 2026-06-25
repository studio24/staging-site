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
use Studio24\StagingSite\StagingSite;

// Display login page on staging
StagingSite::run('ENVIRONMENT', 'staging');
```

This will display the staging site password based on:

* Environment variable: `ENVIRONMENT`
* Staging environments: `staging`
