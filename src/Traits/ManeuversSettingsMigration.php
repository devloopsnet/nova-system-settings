<?php

namespace Devloops\NovaSystemSettings\Traits;

use Spatie\LaravelSettings\Exceptions\MissingSettings;

/**
 * Override all methods to execute a maneuver to avoid creating migrations for the settings.
 * Because settings must have default values in the repository before using.
 *
 * @see https://github.com/spatie/laravel-settings#creating-settings-migrations
 *
 * @mixin \Devloops\NovaSystemSettings\Contracts\SystemSettings
 */
trait ManeuversSettingsMigration
{
    public function __get($name)
    {
        saveSetting:
        try {
            return parent::__get($name);
        } catch (MissingSettings $e) {
            $this->migrateDefaultValues();
            goto saveSetting;
        }
    }

    public function __set($name, $value)
    {
        saveSetting:
        try {
            parent::__set($name, $value);
        } catch (MissingSettings $e) {
            $this->migrateDefaultValues();
            goto saveSetting;
        }
    }

    public function __isset($name)
    {
        saveSetting:
        try {
            return parent::__isset($name);
        } catch (MissingSettings $e) {
            $this->migrateDefaultValues();
            goto saveSetting;
        }
    }

    public function refresh(): self
    {
        saveSetting:
        try {
            return parent::refresh();
        } catch (MissingSettings $e) {
            $this->migrateDefaultValues();
            goto saveSetting;
        }
    }
}
