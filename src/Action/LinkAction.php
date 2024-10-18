<?php

namespace Glint\Glint\Action;

use Closure;

class LinkAction extends Action
{
    private bool $opens_in_new_window = false;

    private Closure|string $url;

    public function url(Closure|string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getLink($record)
    {
        return value($this->url, $record);
    }

    public function openInNewWindow(): static
    {
        $this->opens_in_new_window = true;

        return $this;
    }

    public function opensInNewWindow(): bool
    {
        return $this->opens_in_new_window;
    }

    public function render($record)
    {
        return view('glint::components.row-actions.link-action', [
            'record' => $record,
            'action' => $this,
        ]);
    }
}
