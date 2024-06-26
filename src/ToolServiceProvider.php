<?php

namespace Devloops\NovaSystemSettings;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Devloops\NovaSystemSettings\Http\Middleware\Authorize;
use Spatie\LaravelSettings\SettingsMapper as SpatieSettingsMapper;

/**
 * Class ToolServiceProvider.
 *
 * @package Devloops\NovaSystemSettings
 * @date    05/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(static function (ServingNova $event) {
        });
        $this->app->bind(SpatieSettingsMapper::class, SettingsMapper::class);
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'system-settings')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
             ->prefix('nova-vendor/system-settings')
             ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
