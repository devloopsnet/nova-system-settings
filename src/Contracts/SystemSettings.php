<?php

namespace Devloops\NovaSystemSettings\Contracts;

use Laravel\Nova\Makeable;
use Spatie\LaravelSettings\SettingsMapper;
use Devloops\NovaSystemSettings\Components\Settings;
use Spatie\LaravelSettings\Settings as SpatieSettings;
use Devloops\NovaSystemSettings\Traits\ManeuversSettingsMigration;

/**
 * Class SystemSettings.
 *
 * @package Devloops\NovaSystemSettings\Contracts
 * @date    06/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
abstract class SystemSettings extends SpatieSettings
{
    use Makeable;
    use ManeuversSettingsMigration;

    /**
     * System setting's Settings component.
     *
     * @var \Devloops\NovaSystemSettings\Components\Settings|null
     */
    private ?Settings $settings = null;

    /**
     * Get system settings title.
     *
     * @return string
     */
    abstract public static function title(): string;

    /**
     * Get system settings icon.
     *
     * @return string
     */
    abstract public static function icon(): string;

    /**
     * Get system settings name.
     *
     * @return string
     */
    abstract public static function name(): string;

    /**
     * Return system settings fields.
     *
     * @return array
     */
    abstract public static function fields(): array;


    /**
     * Get the full group name.
     *
     * @return string
     */
    private function getGroup(): string
    {
        return sprintf('%s.%s', static::group(), static::name());
    }

    /**
     * Construct a settings component.
     *
     * @return \Devloops\NovaSystemSettings\Contracts\SettingsContract
     */
    public function getSettingsComponent(): SettingsContract
    {
        if ($this->settings === null) {
            $this->settings = Settings::make(static::title(), static::icon(), static::fields(), $this);
        }
        return $this->settings;
    }

    /**
     * Migrate and set default values.
     *
     * @return void
     */
    public function migrateDefaultValues(): void
    {
        foreach (
            $this->getSettingsComponent()
                 ->getFieldsKeys() as $fieldKey
        ) {
            $this->getRepository()
                 ->createProperty($this->getGroup(), $fieldKey, null);
        }
    }
}
