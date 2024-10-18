<?php

namespace Glint\Glint\Column;

use Glint\Glint\Concerns\CanFormatLabel;
use Glint\Glint\Concerns\CanHide;
use Glint\Glint\Contracts\ColumnInterface;
use Closure;

class Column implements ColumnInterface
{
    use CanFormatLabel, CanHide;

    public $label;

    public $attribute;

    protected bool $sortable = false;

    protected ?string $sortColumn;

    public ?Closure $state_closure = null;

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
        $this->sortColumn = $attribute;
        $this->label = $this->formatLabel($attribute);
    }

    public static function make(string $attribute): static
    {
        return new static($attribute);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sortable(bool|string $sortable = true): static
    {
        if (is_string($sortable)) {
            $this->sortable = true;
            $this->sortColumn = $sortable;
        } else {
            $this->sortable = $sortable;
        }

        return $this;
    }

    public function getStateUsing(Closure $func): static
    {
        $this->state_closure = $func;

        return $this;
    }

    public function getState($record)
    {
        if ($this->state_closure) {
            return value($this->state_closure, $record);
        }

        return \Arr::get($record, $this->attribute);
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getSortColumn(): ?string
    {
        return $this->sortColumn;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }
}
