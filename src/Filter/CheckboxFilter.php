<?php

namespace Glint\Glint\Filter;

use Glint\Glint\Concerns\CanHide;

class CheckboxFilter extends Filter
{
    use CanHide;

    public function processQuery($query, $value)
    {
        if ($this->custom_query) {
            $closure = $this->custom_query;
            $closure($query, $value);
        } else {
            $query->where($this->attribute, $value);
        }
    }

    public function getIndicatorValue(array|int|string $values): string
    {
        return $values ? 'True' : 'False';
    }
}
