<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Symfony;

use Studio24\StagingSite\StagingSite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class StagingSiteEventSubscriber implements EventSubscriberInterface
{
    private StagingSite $staging;

    public function __construct(private KernelInterface $kernel)
    {
        $this->staging = StagingSite::getInstance();
    }

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
        $this->staging->setEnvironment($this->kernel->getEnvironment());
        $this->staging->setStagingEnvironments(['stage', 'staging']);
        if ($this->staging->isStaging() && !$this->staging->authenticate(false)) {
            $response = new Response($this->staging->getLoginPageHtml(), 401);
            $event->setResponse($response);
        }
    }

    public function stagingHttpHeaders(ResponseEvent $event): void
    {
        // Skip on production
        if ($this->kernel->getEnvironment() === 'production') {
            return;
        }

        // Block search engines from indexing staging site
        if ($this->staging->isStaging()) {
            foreach ($this->staging->headers->getHeaders() as $name => $value) {
                $event->getResponse()->headers->set($name, $value, $this->staging->headers->replace($name));
            }
        }
    }
}
