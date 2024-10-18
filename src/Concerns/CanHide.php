<?php

namespace Glint\Glint\Concerns;

use Closure;

trait CanHide
{
    protected bool|Closure $visibility_closure = true;

    public bool $toggledVisible = true;

    protected bool $toggleable = false;

    public function visible(bool|Closure $func): static
    {
        $this->visibility_closure = $func;

        return $this;
    }

    public function isVisible($record = null)
    {
        return value($this->visibility_closure, $record) && $this->toggledVisible;
    }

    public function toggleable(bool $toggleable = true, bool $default_state = true): static
    {
        $this->toggleable = $toggleable;
        $this->toggledVisible = $default_state;

        return $this;
    }

    public function isToggleable()
    {
        return $this->toggleable;
    }

    public function toggleVisible()
    {
        $this->toggledVisible = ! $this->toggledVisible;
    }
}
