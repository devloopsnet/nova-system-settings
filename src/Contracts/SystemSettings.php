<?php

namespace Devloops\NovaSystemSettings\Contracts;

use Laravel\Nova\Makeable;
use Devloops\NovaSystemSettings\Components\Settings;
use Spatie\LaravelSettings\Settings as SpatieSettings;
use Spatie\LaravelSettings\Exceptions\MissingSettings;
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
    abstract public function title(): string;

    /**
     * Get system settings icon.
     *
     * @return string
     */
    abstract public function icon(): string;

    /**
     * Get system settings name.
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * Return system settings fields.
     *
     * @return array
     */
    abstract public function fields(): array;

    /**
     * Construct a settings component.
     *
     * @return \Devloops\NovaSystemSettings\Contracts\SettingsContract
     */
    public function getSettingsComponent(): SettingsContract
    {
        if ($this->settings === null) {
            $this->settings = Settings::make($this->title(), $this->icon(), $this->fields(), $this);
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
                 ->createProperty(static::group(), $fieldKey, null);
        }
    }
}
