<?php

namespace Glint\Glint\Column;

use Glint\Glint\Concerns\CanHide;
use Glint\Glint\Concerns\CanSearch;
use Closure;
use Illuminate\Support\Str;

class TextColumn extends Column
{
    use CanHide, CanSearch;

    protected $url;

    public function url(Closure|string $route): static
    {
        $this->url = $route;

        return $this;
    }

    public function title()
    {
        $attr = $this->attribute;

        $this->state_closure = fn ($i) => Str::title($i->$attr);

        return $this;
    }
}
