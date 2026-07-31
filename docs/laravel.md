# Laravel

This will display the staging site password based on:

* Environment variable: `APP_ENV`
* Staging environments: `staging`, `stage`

## Staging site password

Add the password hash to `.env`

```
STAGING_SITE_PASSWORD="your password hash"
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

## Config

Copy the sample config file into your app:

```
cp vendor/studio24/staging-site/src/Laravel/config/staging-site.php config/staging-site.php
```

## Middleware

Register the Laravel middleware by editing `bootstrap/app.php`

```php
use Studio24\StagingSite\Laravel\StagingSiteMiddleware;
```

Append it to the global middleware stack:

```php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(StagingSiteMiddleware::class);
    })
```

See https://laravel.com/docs/13.x/middleware#registering-middleware

## A note on request handling

Unauthenticated requests never reach your application's routes - the middleware returns the login page directly without 
dispatching the router, regardless of the requested URI or HTTP method. This avoids `405 Method Not Allowed` errors that 
would otherwise occur when the login form (which posts back to the current page) is submitted to a route that doesn't 
accept POST.

On a successful login submission, the middleware issues a `302` redirect back to the same URL as a `GET` request, 
rather than forwarding the login POST into your application. Your routes never see the `staging_site_login`/`password` POST fields.

## Customising options

You can copy the file `vendor/studio24/staging-site/src/Laravel/StagingSiteMiddleware.php` to your project and customise the options.
