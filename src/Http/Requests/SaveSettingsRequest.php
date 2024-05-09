<?php

namespace Devloops\NovaSystemSettings\Http\Requests;

use Closure;
use Laravel\Nova\Http\Requests\NovaRequest;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;

/**
 * Class SaveSettingsRequest.
 *
 * @property string $settingsName
 * @property string $groupName
 *
 * @package Devloops\NovaSystemSettings\Http\Requests
 * @date    06/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class SaveSettingsRequest extends NovaRequest
{
    /**
     * @var \Closure|null
     */
    public static ?Closure $rulesResolver = null;

    /**
     * @var \Closure|null
     */
    public static ?Closure $settingsResolver = null;

    /**
     * Return request fields rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return (self::$rulesResolver ?? static fn(string $groupName, string $settings, self $request) => [])($this->groupName, $this->settingsName, $this);
    }

    /**
     * Return settings object.
     *
     * @return \Devloops\NovaSystemSettings\Contracts\SystemSettings|null
     */
    public function getSettings(): ?SystemSettings
    {
        return (self::$settingsResolver ?? static fn(string $groupName, string $settings): ?SystemSettings => null)($this->groupName, $this->settingsName);
    }
}
