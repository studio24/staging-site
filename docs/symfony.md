# Symfony

This will display the staging site password based on:

* Environment variable: `APP_ENV`
* Staging environments: `staging`, `stage`

## Staging site password

Add the password hash to `.env.local`

```
STAGING_SITE_PASSWORD="your password hash"
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

## Event listener

Register the Symfony event listener by editing `config/services.yaml`

```php
# config/services.yaml
services:
    Studio24\StagingSite\Symfony\StagingSiteEventSubscriber:
        tags: [kernel.event_subscriber]
```

## Customising options

You can copy the file `vendor/studio24/staging-site/src/Symfony/StagingSiteEventListener.php` to your project and customise the options.
