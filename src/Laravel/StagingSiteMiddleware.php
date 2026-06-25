<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Laravel;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Studio24\StagingSite\StagingSite;

class StagingSiteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip on production
        if (App::environment('production')) {
            return $next($request);
        }

        // Staging site authentication
        $staging = StagingSite::getInstance();
        $staging->setEnvironment(App::environment());

        $staging->setStagingEnvironments(['stage', 'staging']);
        if ($staging->isStaging() && !$staging->authenticate(false)) {
            $response = $next($request);
            $response->setStatusCode(401);
            $response->setContent($staging->getLoginPageHtml());

            // Block search engines from indexing staging site
            foreach ($staging->headers->getHeaders() as $name => $value) {
                $response->headers->set($name, $value, $staging->headers->replace($name));
            }

            return $response;
        }

        return $next($request);
    }
}
