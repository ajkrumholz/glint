@php
    // Data tables display differently based on the presence of filters, search, etc.
    $show_data_table_header = $showSearch || !empty($headerActions) || !empty($tableFilters) || !empty($toggles);
@endphp

<div class="glint" x-data="{
    filterMessage: '',
    updateFilterMessage(message) {
        this.filterMessage = message
    }
}">
    @if ($show_data_table_header)
        <div class="glint-header">

            {{-- Filtering --}}
            <form wire:submit.prevent class="form glint-filtering">
                @if ($showSearch)
                    <x-glint.search />
                @endif
                @foreach ($headerActions as $action)
                    <x-glint.header-action :$action />
                @endforeach

                @if (!empty($tableFilters))
                    <x-glint.filters :$tableFilters />
                @endif

                {{-- Toggles --}}
                @if (!empty($toggleColumns))
                  <x-glint.toggle-columns :$toggleColumns />
                @endif
            </form>

            {{-- Filter Indicator Display --}}
            @if (!empty($tableFilters))
                <x-glint.filter-indicators :$tableFilters :$filters />
            @endif
        </div>
    @endif

    {{-- Data Table --}}
    <div class="glint-table">
        <div wire:loading.grid class="glint-loading">
            <div class="glint-loading__overlay"></div>
            <div class="glint-loading__message" role="status">
                <span class="loader"></span>
                <span class="visually-hidden">Loading…</span>
            </div>
        </div>

        {{-- Messages for assistive tech --}}
        <div class="visually-hidden" x-text="filterMessage" role="status"></div>

        <table>
            <thead>
                <tr>
                    {{-- TODO: Toggleable fields --}}
                    @foreach ($columns as $column)
                        @if ($column->isVisible())
                            <x-glint.header key="{{ $loop->index }}" :$column :$sortColumn :$sortAsc />
                        @endif
                    @endforeach

                    @if (!empty($rowActions))
                        <x-glint.header key="actions" label="Actions" center />
                    @endif
                </tr>
            </thead>

            @if ($collection->isNotEmpty())
                <tbody>
                    @foreach ($collection as $item)
                        <tr>
                            @foreach ($columns as $column)
                                @if ($column->isVisible($item))
                                    <td>{{ $column->getState($item) }}</td>
                                @endif
                            @endforeach

                            @if (!empty($rowActions))
                                <x-glint.row-actions :$rowActions :$item />
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            @endif
        </table>
    </div>

    @if ($collection->isEmpty())
        <p class="no-results">{{ $emptyStateMessage }}</p>
    @endif

    {{-- Pagination --}}
    <x-glint.pagination :$collection :$perPageOptions />
</div>
