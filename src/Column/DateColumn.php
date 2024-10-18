<?php

namespace Glint\Glint\Column;

class DateColumn extends Column
{
    private ?string $format = null;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getState($record)
    {
        $state = parent::getState($record);

        if (is_null($state)) {
            return null;
        }

        return $this->format ? $state->format($this->format) : $state;
    }
}
