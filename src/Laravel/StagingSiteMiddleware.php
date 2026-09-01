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

        // Skip if not staging
        $staging = StagingSite::getInstance();
        $staging->setEnvironment(App::environment());
        $staging->setStagingEnvironments(['stage', 'staging']);
        if (!$staging->isStaging()) {
            return $next($request);
        }

        // Check staging authentication
        $staging->auth->setPasswordHash(config('staging-site.password'));
        $isLoginAttempt = $request->isMethod('post') && $request->filled('staging_site_login');
        $authenticated = $staging->authenticate(false);

        if (!$authenticated) {
            // Create new response for login page
            $response = new Response($staging->getLoginPageHtml(), 401);
            foreach ($staging->headers->getHeaders() as $name => $value) {
                $response->headers->set($name, $value, $staging->headers->replace($name));
            }
            return $response;
        }

        if ($isLoginAttempt) {
            // If login attempt, redirect to requested page since real route may not accept POST
            return redirect($request->fullUrl());
        }

        return $next($request);
    }
}
