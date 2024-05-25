<?php

namespace Devloops\NovaSystemSettings\Http\Controllers;

use Exception;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\SettingsConfig;
use Laravel\Nova\Http\Requests\NovaRequest;
use Devloops\NovaSystemSettings\NovaSystemSettings;
use Spatie\LaravelSettings\Exceptions\MissingSettings;
use Devloops\NovaSystemSettings\Http\Requests\SaveSettingsRequest;

/**
 * Class ApiController.
 *
 * @package Devloops\NovaSystemSettings\Http\Controllers
 * @date    06/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class ApiController extends Controller
{
    /**
     * Load system settings.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadSettings(NovaRequest $request): JsonResponse
    {
        /**
         * @var $tool NovaSystemSettings
         */
        $tool = collect(Nova::$tools)->first(fn(Tool $tool) => $tool instanceof NovaSystemSettings);
        $groups = $tool->getSettingsGroups(true);
        foreach ($groups[$request->input('activeGroup')]['settings'] as $settingsName => $settings) {
            $settings->setActiveTab($settingsName === $request->input('activeTab'));
        }
        return response()->json([
            'systemSettings' => [
                'groups'      => $groups,
                'activeGroup' => $request->input('activeGroup', $groups[0] ?? []['groupName'] ?? ''),
                'activeTab'   => $request->input('activeTab'),
            ],
        ]);
    }

    /**
     * Handle save settings request.
     *
     * @param \Devloops\NovaSystemSettings\Http\Requests\SaveSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function saveSettings(SaveSettingsRequest $request): JsonResponse
    {
        $settings = $request->getSettings();

        $settingsConfig = new SettingsConfig($settings::class);
        if ($settings === null) {
            abort(404);
        }

        $settingsKeys = $settings?->getSettingsComponent()
                                 ?->getFieldsKeys();

        try {
            foreach ($settingsKeys as $settingsKey) {
                if (property_exists($settings, $settingsKey)) {
                    $cast = $settings::casts()[$settingsKey] ?? null;
                    if ($cast !== null) {
                        $settings->{$settingsKey} = $settingsConfig->getCast($settingsKey)
                                                                   ?->get($request->input($settingsKey));
                    } elseif ($file = $request->file($settingsKey)) {
                        $path = sprintf('%s/%s/%s', $settings::group(), $settings::name(), strtolower(Str::of($settingsKey)
                                                                                                         ->ucsplit()
                                                                                                         ->join('_')));
                        $settings->{$settingsKey} = $file->store($path);
                    } else {
                        $settings->{$settingsKey} = $request->input($settingsKey);
                    }
                }
            }

            $settings->save();
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
