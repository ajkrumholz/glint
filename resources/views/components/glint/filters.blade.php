@props(['tableFilters'])

<div x-data="{ showFilters: false, close() { this.showFilters = false } }" @focusin.window="! $root.contains($event.target) && close()" @keydown.escape.window="close()"
    class="glint-filters" x-id="['filters']">
    <button class="glint-button filter-button" type="button" @click.prevent="showFilters = !showFilters"
        :aria-expanded="showFilters" :aria-controls="$id('filters')" :id="$id('filters', 'button')">
        <x-heroicon-s-funnel class="icon" />
        <span>Filters</span>
    </button>
    <div x-cloak x-show="showFilters" class="filters" @click.outside="close()" :id="$id('filters')"
        :aria-labelledby="$id('filters', 'button')">
        @foreach ($tableFilters as $filter)
            <div wire:key="{{ $this->getTableName() . '-' . $filter->attribute }}">
                {{ $filter->render() }}
            </div>
        @endforeach
    </div>
</div>
