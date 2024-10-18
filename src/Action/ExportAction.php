<?php

namespace Glint\Glint\Action;

class ExportAction extends Action
{
    public static function make(string $label = 'Export'): static
    {
        return new static($label);
    }
}
