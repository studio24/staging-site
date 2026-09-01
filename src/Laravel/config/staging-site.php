<?php

return [
    // Staging site password hash — generate with php vendor/bin/password-hash.php or password_hash()
    'password' => env('STAGING_SITE_PASSWORD'),
];
