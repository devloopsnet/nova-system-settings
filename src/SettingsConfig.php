<?php

namespace Devloops\NovaSystemSettings;

use Illuminate\Support\Collection;
use Spatie\LaravelSettings\SettingsConfig as SpatieSettingsConfig;

/**
 * Class SettingsConfig.
 *
 * @package Devloops\NovaSystemSettings
 * @date    09/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class SettingsConfig extends SpatieSettingsConfig
{
    public function getGroup(): string
    {
        /**
         * @var $settingsClass \Spatie\LaravelSettings\Settings
         */
        $settingsClass = $this->getName();
        return sprintf('%s.%s', $settingsClass::group(), $settingsClass::name());
    }

    public function getLocked(): Collection
    {
        if (!empty($this->locked)) {
            return $this->locked;
        }

        return $this->locked = collect($this->getRepository()
                                            ->getLockedProperties($this->getGroup()));
    }
}
