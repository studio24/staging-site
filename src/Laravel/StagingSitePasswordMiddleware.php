<?php
declare(strict_types=1);

namespace Studio24\StagingSite\Laravel;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Studio24\StagingSite\Controller as StagingSiteController;

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

        StagingSiteController::run('laravel');
        return $next($request);
    }
}
