<?php
declare(strict_types=1);

namespace Studio24\StagingSite\Symfony;

use Studio24\StagingSite\Controller as StagingSiteController;
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

        StagingSiteController::run('symfony');
    }
}
