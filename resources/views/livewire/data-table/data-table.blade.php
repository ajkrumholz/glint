@php
    // Data tables display differently based on the presence of filters, search, etc.
    $show_data_table_header = $showSearch || !empty($headerActions) || !empty($tableFilters) || !empty($toggles);
@endphp

<div class="data-table" x-data="{
    filterMessage: '',
    updateFilterMessage(message) {
        this.filterMessage = message
    }
}">
    @if ($show_data_table_header)
        <div class="data-table-header">

            {{-- Filtering --}}
            <form wire:submit.prevent class="form data-table-filtering">
                @if ($showSearch)
                    <x-data-table.search />
                @endif
                @foreach ($headerActions as $action)
                    <x-data-table.header-action :$action />
                @endforeach

                @if (!empty($tableFilters))
                    <x-data-table.filters :$tableFilters />
                @endif

                {{-- Toggles --}}
                @if (!empty($toggleColumns))
                  <x-data-table.toggle-columns :$toggleColumns />
                @endif
            </form>

            {{-- Filter Indicator Display --}}
            @if (!empty($tableFilters))
                <x-data-table.filter-indicators :$tableFilters :$filters />
            @endif
        </div>
    @endif

    {{-- Data Table --}}
    <div class="data-table-table">
        <div wire:loading.grid class="data-table-loading">
            <div class="data-table-loading__overlay"></div>
            <div class="data-table-loading__message" role="status">
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
                            <x-data-table.header key="{{ $loop->index }}" :$column :$sortColumn :$sortAsc />
                        @endif
                    @endforeach

                    @if (!empty($rowActions))
                        <x-data-table.header key="actions" label="Actions" center />
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
                                <x-data-table.row-actions :$rowActions :$item />
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
    <x-data-table.pagination :$collection :$perPageOptions />
</div>
