<?php

namespace Glint\Glint\Filter;

use Glint\Glint\Concerns\CanHide;
use Carbon\Carbon;

class DateFilter extends Filter
{
    use CanHide;

    public ?string $default_start = null;

    public ?string $default_end = null;

    public function processQuery($query, $value)
    {
        $start = $value['start'] ? Carbon::parse($value['start']) : null;
        $end = $value['end'] ? Carbon::parse($value['end']) : null;

        if (! $start && ! $end) {
            return;
        }

        if ($start && ! $end) {
            $query->where($this->attribute, '>=', $start);
        } elseif ($end) {
            $query->where($this->attribute, '<=', $end);
        } else {
            $query->whereBetween($this->attribute, [$start, $end]);
        }
    }

    public function getIndicatorValue(array|int|string $values): string
    {
        return $values ? 'True' : 'False';
    }

    public function defaultStart(Carbon|string $date): static
    {
        $this->default_start = $this->parseDate($date);

        return $this;
    }

    public function defaultEnd(Carbon|string $date): static
    {
        $this->default_end = $this->parseDate($date);

        return $this;
    }

    private function parseDate(Carbon|string $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $date->format('Y-m-d');
    }

    public function render()
    {
        return view('components.data-table.filters.date-filter', [
            'filter' => $this,
        ]);
    }
}
