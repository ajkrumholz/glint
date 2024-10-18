<?php

namespace Glint\Glint\Filter;

use Glint\Glint\Concerns\CanHide;
use Closure;

class SelectFilter extends Filter
{
    use CanHide;

    protected bool $multiple = false;

    // protected ?Closure $custom_query = null;

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    // public function queryUsing(Closure $closure): static
    // {
    //     $this->custom_query = $closure;

    //     return $this;
    // }

    public function processQuery($query, $value)
    {
        if (empty($value) || (is_array($value) && ! isset($value[0])) || $value[0] === 'all') {
            return;
        }

        if ($this->relationship) {
            $this->relationshipQuery($query, $value);
        } elseif ($this->custom_query) {
            // This is a bit hacky, but allows us to maintain a consistent datatype for values (array) while not making custom queries on single selects super ugly. Not sure this is the right approach, my longer term preference would be to more clearly separate single and multiple selects, but this is ok for now - AJK 08/12/2024
            if (! $this->multiple && count($value) <= 1) {
                $value = $value[0] ?? null;
            }
            $this->customQuery($query, $value);
        } else {
            $this->defaultQuery($query, $value);
        }
    }

    protected function customQuery($query, $value)
    {
        $closure = $this->custom_query;
        $closure($query, $value);
    }

    protected function defaultQuery($query, array $value)
    {
        $table = $query->getModel()->getTable();
        $attribute = "$table.$this->attribute";
        $query->whereIn($attribute, $value);
    }

    protected function relationshipQuery($query, array $value)
    {
        $query->whereHas($this->relationship, function ($query) use ($value) {
            $query->whereIn($this->attribute, $value);
        });
    }

    public function render()
    {
        return view('components.data-table.filters.select-filter', [
            'filter' => $this,
        ]);
    }
}
