@props(['tableFilters', 'filters'])

@php
    $activeFilters = collect($tableFilters)->filter(function ($filter) {
        $value = $this->filters[$filter->attribute] ?? null;
        return is_array($value) ? !empty($value[0]) : !empty($value);
    });
@endphp

@if ($activeFilters->isNotEmpty())
    <div class="glint-filtered-by" role="status">
        @foreach ($activeFilters as $filter)
            @php
                $label = $filter->getLabel();
                $value = $filter->getIndicatorValue($filters[$filter->attribute]);
            @endphp
            <span class="filter-name">{{ $label . ': ' }}</span>{{ $value }}
        @endforeach
    </div>
@endif
