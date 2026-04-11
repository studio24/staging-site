<?php
declare(strict_types=1);

namespace Studio24\StagingSite\Symfony;

use Studio24\StagingSite\StagingSite;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class StagingSiteEventListener
{
    public function __invoke(RequestEvent $event): void
    {
        // Skip on production or not the main request
        if ($this->kernel->getEnvironment() === 'production') {
            return;
        }
        if (!$event->isMainRequest()) {
            return;
        }

        StagingSite::run('APP_ENV', ['stage', 'staging']);
    }
}
