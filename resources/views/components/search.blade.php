<div x-data class="glint-search" @click="$focus.focus($refs.searchInput)">
    <x-heroicon-o-magnifying-glass class="glint-search__icon" />
    <label class="visually-hidden" for="search">Search</label>
    <input x-ref="searchInput" type="search" id="search" wire:model.live="search" wire:model.live.debounce.250ms="search" autocomplete="off" placeholder="Search…">
</div>
