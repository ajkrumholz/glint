<?php

namespace Glint\Glint\Action;

use Glint\Glint\Concerns\CanHide;
use Glint\Glint\Contracts\ActionInterface;
use Closure;

class Action implements ActionInterface
{
    use CanHide;

    public $label;

    protected $action;

    final public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): static
    {
        return new static($label);
    }

    public function getLabel($record = null): string
    {
        return value($this->label, $record);
    }

    public function label(Closure|string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
