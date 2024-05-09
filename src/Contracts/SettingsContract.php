<?php

namespace Devloops\NovaSystemSettings\Contracts;

use Closure;

interface SettingsContract
{
    /**
     * The title of the tab
     *
     * This will be used to as the tab title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title): static;

    /**
     * The icon of the tab.
     *
     * This will be shown next to the tab's title.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon(string $icon): static;

    /**
     * The name of the tab
     *
     * This will be used to remember which tab is selected
     * If the name is not supplied, the sluggified tab title is used
     *
     * @param string $name
     *
     * @return $this
     */
    public function name(string $name): static;

    /**
     * Get system settings title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get system settings name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get system settings icon.
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * A boolean or function that returns a boolean
     *
     * If the result is true, the tab will be shown
     *
     * showIf takes priority over showUnless
     *
     * @param bool | Closure $condition
     *
     * @return $this
     */
    public function showIf(bool|Closure $condition): static;

    /**
     * A boolean or function that returns a boolean
     *
     * If the result is false, the tab will be shown
     *
     * showIf takes priority over showUnless
     *
     * @param bool | Closure $condition
     *
     * @return $this
     */
    public function showUnless(bool|Closure $condition): static;

    /**
     * Array representation of the tab
     *
     * @return array
     */
    public function toArray(): array;
}
