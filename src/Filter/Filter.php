<?php

namespace Glint\Glint\Filter;

use Glint\Glint\Concerns\CanFormatLabel;
use Glint\Glint\Concerns\CanHide;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class Filter
{
    use CanFormatLabel, CanHide;

    public string $label;

    public string $attribute;

    public string $id;

    public array $options;

    public mixed $default = null;

    public ?string $relationship = null;

    protected ?Closure $custom_query = null;

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
        $this->label = $this->formatLabel($attribute);
        $this->id = uniqid();
    }

    public static function make(string $attribute): static
    {
        // @phpstan-ignore-next-line
        return new static($attribute);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function options(Collection|array $options): static
    {
        if ($options instanceof Collection) {
            $options = $options->toArray();
        }

        if (Arr::isList($options)) {
            $options = array_combine($options, $options);
        }

        $this->options = $options;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = is_array($default) ? $default : [$default];

        return $this;
    }

    public function relationship(string $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIndicatorValue(array $values): string
    {
        $indicators = array_map(fn ($value) => $this->options[$value] ?? $value, $values);

        return implode(', ', $indicators);
    }

    public function queryUsing(Closure $closure): static
    {
        $this->custom_query = $closure;

        return $this;
    }

    // ------------------- Abstract Functions ------------------- //
    abstract public function processQuery($query, $value);
}
