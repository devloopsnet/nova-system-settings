<?php

namespace Devloops\NovaSystemSettings;

use Closure;
use Stripe\Util\Set;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Http\Requests\NovaRequest;
use Devloops\NovaSystemSettings\Components\Settings;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;
use Devloops\NovaSystemSettings\Contracts\SettingsContract;
use Spatie\LaravelSettings\SettingsMapper as SpatieSettingsMapper;
use Devloops\NovaSystemSettings\Http\Requests\SaveSettingsRequest;

/**
 * Class NovaSystemSettings.
 *
 * @package Devloops\NovaSystemSettings
 * @date    05/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class NovaSystemSettings extends Tool
{
    /**
     * @param array $systemSettings
     */
    public function __construct(
        public readonly array $systemSettings = []
    ) {
        parent::__construct();
    }

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot(): void
    {
        Nova::script('system-settings', __DIR__.'/../dist/js/tool.js');
        Nova::style('system-settings', __DIR__.'/../dist/css/tool.css');
        $this->pushSettingsGroupToJs();
        Config::set('settings.settings', collect($this->systemSettings)
            ->map(fn(SystemSettings $settings) => $settings::class)
            ->toArray());
        SaveSettingsRequest::$rulesResolver = $this->prepareSettingsRulesResolver();
        SaveSettingsRequest::$settingsResolver = $this->prepareSettingsResolver();
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     * @throws \Laravel\Nova\Exceptions\NovaException
     */
    public function menu(Request $request): mixed
    {
        return MenuSection::make(__('System Settings'))
                          ->path('/system-settings')
                          ->icon('cog');
    }

    /**
     * Group settings to send them to frontend.
     *
     * @param array $systemSettings
     * @param bool  $forJs
     *
     * @return array
     */
    private function groupSettings(array $systemSettings, bool $forJs = false): array
    {
        $i = 0;
        return collect($systemSettings)
            ->groupBy(fn(SystemSettings $settings) => $settings::group())
            ->map(fn(Collection $settings, string $settingsGroup) => [
                'groupName'  => $settingsGroup,
                'groupTitle' => __("system-settings.groups.$settingsGroup"),
                'settings'   => $settings->each(function (SystemSettings $settings) use (&$i) {
                    $settings->getSettingsComponent()
                             ->setActiveTab($i++ === 0);
                }),
            ])
            ->mapWithKeys(function (array $settings) use ($forJs) {
                $settings['settings'] = $settings['settings']->when($forJs, function (Collection $settings) {
                    return $settings->map(fn(SystemSettings $systemSettings) => $systemSettings->getSettingsComponent());
                })
                                                             ->mapWithKeys(function (SystemSettings|SettingsContract $settings) {
                                                                 if ($settings instanceof SettingsContract) {
                                                                     return [
                                                                         $settings->getName() => $settings,
                                                                     ];
                                                                 }
                                                                 return [
                                                                     $settings->name() => $settings,
                                                                 ];
                                                             });
                return [
                    $settings['groupName'] => $settings,
                ];
            })
            ->toArray();
    }

    /**
     * Prepares the resolver callback which will resolve the settings form rules.
     *
     * @return \Closure
     */
    private function prepareSettingsRulesResolver(): Closure
    {
        return function (string $groupName, string $settingsName, NovaRequest $request): array {
            $rules = [];
            /**
             * @var $component Settings
             */
            $component = $this->getSettingsGroups(true)[$groupName]['settings'][$settingsName];

            foreach ($component->getFields() as $field) {
                $rules = [
                    ...$rules,
                    ...$field->getRules($request),
                ];
            }

            return $rules;
        };
    }

    /**
     * Prepare the resolver callback which will resolve the settings by settings name.
     *
     * @return \Closure
     */
    private function prepareSettingsResolver(): Closure
    {
        return function (string $groupName, string $settingsName): ?SystemSettings {
            return $this->getSettingsGroups()[$groupName]['settings'][$settingsName] ?? null;
        };
    }

    /**
     * Pushes the system settings group to the frontend js.
     *
     * @return void
     */
    private function pushSettingsGroupToJs(): void
    {
        $settingsGroups = $this->getSettingsGroups(true);
        Nova::provideToScript([
            'systemSettings' => [
                'groups'      => $settingsGroups,
                'activeGroup' => $settingsGroups[array_key_first($settingsGroups)]['groupName'] ?? '',
            ],
        ]);
    }

    /**
     * Return settings group.
     *
     * @param bool $forJs
     *
     * @return array
     */
    public function getSettingsGroups(bool $forJs = false): array
    {
        return $this->groupSettings($this->systemSettings, $forJs);
    }
}
