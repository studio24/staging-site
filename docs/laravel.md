# Laravel

This will display the staging site password based on:

* Environment variable: `APP_ENV`
* Non-staging environments it ignores: production, local

## Staging site password

Add the password hash to `.env`

```
STAGING_SITE_PASSWORD="your password hash"
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

## Middleware

Register the Laravel middleware by editing `bootstrap/app.php`

```php
use Studio24\StagingSitePassword\Laravel\StagingSitePasswordMiddleware;
```

Append it to the global middleware stack:

```php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(StagingSitePasswordMiddleware::class);
    })
```

See https://laravel.com/docs/13.x/middleware#registering-middleware

