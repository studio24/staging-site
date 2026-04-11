<?php
declare(strict_types=1);

namespace Studio24\StagingSite\CraftCms;

use Craft;
use Studio24\StagingSite\StagingSite;
use yii\base\Module;

class StagingSiteModule extends Module
{
    public function init()
    {
        parent::init();

        // Skip on production
        if (Craft::$app->env === 'production') {
            return;
        }

        Craft::$app->on(Application::EVENT_BEFORE_ACTION, function(Event $event) {
            StagingSite::run('CRAFT_ENVIRONMENT', ['stage', 'staging']);
        });
    }
}

