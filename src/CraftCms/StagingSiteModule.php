<?php
declare(strict_types=1);

namespace Studio24\StagingSite\CraftCms;

use Craft;
use Studio24\StagingSite\Headers;
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
            // Staging site authentication
            $staging = new StagingSite();
            $staging->setEnvironment(Craft::$app->env);
            $staging->setStagingEnvironments(['stage', 'staging']);
            if ($staging->isStaging()) {
                $staging->authenticate();

                // Block search engines from indexing staging site
                foreach ($staging->getHeaders() as $name => $value) {
                    Craft::$app->getResponse()->getHeaders()->add($name, $value);
                }
            }
        });
    }
}

