<?php

namespace Devloops\NovaSystemSettings\Http\Middleware;

use Closure;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Devloops\NovaSystemSettings\NovaSystemSettings;

/**
 * Class Authorize.
 *
 * @package Devloops\NovaSystemSettings\Http\Middleware
 * @date    06/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Closure(\Illuminate\Http\Request):mixed $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param \Laravel\Nova\Tool $tool
     *
     * @return bool
     */
    public function matchesTool(Tool $tool): bool
    {
        return $tool instanceof NovaSystemSettings;
    }
}
