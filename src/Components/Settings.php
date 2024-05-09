<?php

namespace Devloops\NovaSystemSettings\Components;

use Closure;
use JsonSerializable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Devloops\NovaSystemSettings\Contracts\SystemSettings;
use Devloops\NovaSystemSettings\Contracts\SettingsContract;

/**
 * Class Settings.
 *
 * @package Devloops\NovaSystemSettings\Components
 * @date    05/05/2024
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Settings implements SettingsContract, JsonSerializable, Arrayable
{
    /**
     * @var string|null
     */
    protected ?string $name;

    /**
     * @var bool|Closure|null
     */
    protected Closure|bool|null $showIf = null;

    /**
     * @var bool|Closure|null
     */
    protected Closure|bool|null $showUnless = null;

    /**
     * @var bool
     */
    protected bool $activeTab = false;

    /**
     * Initialize the settings object.
     *
     * @param string|\Closure                                       $title
     * @param string                                                $icon
     * @param array                                                 $fields
     * @param \Devloops\NovaSystemSettings\Contracts\SystemSettings $systemSettings
     */
    public function __construct(
        protected string|Closure $title,
        protected string $icon,
        protected array $fields,
        protected SystemSettings $systemSettings
    ) {
        $this->name = Str::slug($title, '_');
    }

    /**
     * Initiate a new settings object.
     *
     * @param mixed                                                 $title
     * @param string                                                $icon
     * @param array                                                 $fields
     * @param \Devloops\NovaSystemSettings\Contracts\SystemSettings $systemSettings
     *
     * @return self
     */
    public static function make(mixed $title, string $icon, array $fields, SystemSettings $systemSettings): self
    {
        return new static($title, $icon, $fields, $systemSettings);
    }

    /**
     * The title of the tab
     *
     * This will be used to as the tab title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * The icon of the tab.
     *
     * This will be shown next to the tab's title.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * The name of the tab
     *
     * This will be used to remember which tab is selected
     * If the name if not supplied, the sluggified tab title is used
     *
     * @param string $name
     *
     * @return $this
     */
    public function name(string $name): static
    {
        $this->name = Str::slug($name, '_');

        return $this;
    }

    /**
     * Get settings title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->resolve($this->title);
    }

    /**
     * Get settings icon.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get settings name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? $this->getTitle();
    }

    /**
     * Get settings fields.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFields(): Collection
    {
        return collect($this->fields)->map(function (Field $field) {
            $field->value = $this->systemSettings->{$field->attribute} ?? null;
            return $field;
        });
    }

    /**
     * Return fields keys array.
     *
     * @return array
     */
    public function getFieldsKeys(): array
    {
        $fieldsKeys = [];
        foreach ($this->fields as $field) {
            $fieldsKeys[] = $field->attribute;
        }
        return $fieldsKeys;
    }

    /**
     * Check if active tab.
     *
     * @return bool
     */
    public function isActiveTab(): bool
    {
        return $this->activeTab;
    }

    /**
     * Set active tab.
     *
     * @param bool $activeTab
     *
     * @return void
     */
    public function setActiveTab(bool $activeTab): void
    {
        $this->activeTab = $activeTab;
    }

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
    public function showIf(bool|Closure $condition): static
    {
        if (is_bool($condition) || is_callable($condition)) {
            $this->showIf = $condition;

            return $this;
        }

        throw new InvalidArgumentException('The $condition parameter must be a boolean or a closure returning one');
    }

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
    public function showUnless(bool|Closure $condition): static
    {
        if (is_bool($condition) || is_callable($condition)) {
            $this->showUnless = $condition;

            return $this;
        }

        throw new InvalidArgumentException('The $condition parameter must be a boolean or a closure returning one');
    }

    /**
     * Checks if the settings should be shown.
     *
     * @return bool
     */
    public function shouldShow(): bool
    {
        if ($this->showIf !== null) {
            return $this->resolve($this->showIf);
        }

        if ($this->showUnless !== null) {
            return !$this->resolve($this->showUnless);
        }

        return true;
    }

    /**
     * Resolve value.
     *
     * @param $value
     *
     * @return mixed
     */
    private function resolve($value): mixed
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    /**
     * Return data for serializer.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create an array from the object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'      => $this->getTitle(),
            'icon'       => $this->getIcon(),
            'name'       => $this->getName(),
            'shouldShow' => $this->shouldShow(),
            'activeTab'  => $this->isActiveTab(),
            'fields'     => $this->getFields(),
        ];
    }
}
