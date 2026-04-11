<?php

// Errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer
require_once '../../vendor/autoload.php';

// Environment
define('ENVIRONMENT', 'staging');

// Staging password
\Studio24\StagingSitePassword\Controller::run('generic');
//\Studio24\StagingSitePassword\Controller::run('generic', '$2y$12$0blURxhiwhm6BT6t3oHDUuUb.Z6k71KAUVTmSNbDpU4kN0vFuksuC');

// Logged in
echo "<p>All OK!</p>";
