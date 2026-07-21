<?php

if (!defined( 'WP_CLI' ) || !WP_CLI) {
    \Studio24\StagingSite\StagingSite::run('WP_ENVIRONMENT_TYPE', 'staging');
}
