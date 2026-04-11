<?php

// Errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer
require_once __DIR__ . '/../../vendor/autoload.php';

use Studio24\StagingSite\StagingSite;

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'staging');
}

// test123
define('STAGING_SITE_PASSWORD', '$2y$12$ZFEMuyyQZFqJhaqt9C8bn.UKZz0EXxoo8t9L7Q98qD1ldJYScAyDe');

// Display login page on staging
StagingSite::run();

// Block search engines from indexing staging site
StagingSite::headers();
