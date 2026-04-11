# Craft CMS

These instructions are for Craft CMS v4 or v5.

This will display the staging site password based on:

* Environment variable: `CRAFT_ENVIRONMENT`
* Staging environments: `staging`, `stage`

## Staging site password

Add the password hash to `.env`

```
STAGING_SITE_PASSWORD="your password hash"
```

Replacing `your password hash` with the password hash string generated in the "create a password hash" step.

## Module

Register the Craft module by editing `config/app.php`:

```php
return [
    'modules' => [
        'staging-site-password' => Studio24\StagingSite\CraftCms\StagingSiteModule::class,
    ],
    'bootstrap' => [
        'staging-site-password',
    ],
];
```

See https://craftcms.com/docs/5.x/extend/module-guide.html#update-the-application-config

## Customising options

You can copy the file `vendor/studio24/staging-site/src/CraftCms/StagingSiteModule.php` to your project and customise the options.
