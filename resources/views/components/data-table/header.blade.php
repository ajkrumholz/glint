@props([
    'column' => null,
    'label' => '',
    'key' => uniqid(),
    'center' => false, // Center the text in the column
    'sortColumn' => null,
    'sortAsc' => true,
])

@php
    $is_sortable = $column && $column->isSortable();
    $is_active = $is_sortable && $sortColumn == $column->getSortColumn();
@endphp

<th @class(['data-table-column-header', 'center' => $center]) wire:key="{{ $key }}"
    {{-- aria-sort on active columns only for a11y, and it's used to target it in CSS --}}
    @if ($is_active) aria-sort="{{ $sortAsc == 'asc' ? 'ascending' : 'descending' }}" @endif>

    @if ($is_sortable)
        <button wire:click.prevent="sortBy('{{ $column->getSortColumn() }}'),
            updateFilterMessage('Sorted by {{ $column->getLabel() }} {{$sortAsc == 'asc' ? 'ascending' : 'descending'}}')" type="button" class="sort-button">
            {{ $column->getLabel() }}
            <svg class="sort-icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 20">
                <polygon points="0 12 7 20 14 12 0 12" />
                <polygon points="0 8 7 0 14 8 0 8" />
            </svg>
        </button>
    @else
        {{ $column ? $column->getLabel() : $label }}
    @endif
</th>
