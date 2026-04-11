<?php
declare(strict_types=1);

namespace Studio24\StagingSite\Symfony;

use Studio24\StagingSite\StagingSite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class StagingSiteEventSubscriber implements EventSubscriberInterface
{
    private StagingSite $staging;

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            RequestEvent::class => 'stagingLogin',
            ResponseEvent::class => 'stagingHttpHeaders',
        ];
    }

    public function stagingLogin(RequestEvent $event): void
    {
        // Skip on production or not the main request
        if ($this->kernel->getEnvironment() === 'production') {
            return;
        }
        if (!$event->isMainRequest()) {
            return;
        }

        // Staging site authentication
        $this->staging = new StagingSite();
        $this->setEnvironment($this->kernel->getEnvironment());
        $this->setStagingEnvironments(['stage', 'staging']);
        if ($this->isStaging()) {
            $this->authenticate();
        }
    }

    public function stagingHttpHeaders(ResponseEvent $event): void
    {
        // Skip on production
        if ($this->kernel->getEnvironment() === 'production') {
            return;
        }

        // Block search engines from indexing staging site
        if ($this->isStaging()) {
            foreach ($this->staging->headers->getHeaders() as $name => $value) {
                $event->headers->set($name, $value, $this->staging->headers->replace($name));
            }
        }
    }

}
