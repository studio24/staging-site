<?php
declare(strict_types=1);

namespace Studio24\StagingSite\Laravel;

use Closure;
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
        if (app()->environment('production')) {
            return $next($request);
        }

        // Staging site authentication
        $staging = new StagingSite();
        $staging->setEnvironment(env('APP_ENV'));
        $staging->setStagingEnvironments(['stage', 'staging']);
        if ($staging->isStaging()) {
            $staging->authenticate();

            // Block search engines from indexing staging site
            $response = $next($request);
            foreach ($staging->headers->getHeaders() as $name => $value) {
                $response->headers->set($name, $value, $staging->headers->replace($name));
            }

            return $response;
        }

        return $next($request);
    }
}
