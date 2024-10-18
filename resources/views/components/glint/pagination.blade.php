{{-- TODO: DataTable controller is breaking previous/next because `$this->goToPage(1);` --}}

@if ($collection->total() >= $perPageOptions[0])

    <div class="glint-footer">

        {{-- The count --}}
        <p class="page-total">Showing {{ $collection->firstItem() }} to {{ $collection->lastItem() }} of {{ $collection->total() }}</p>

        {{-- Per page select --}}
        <div class="page-select">
            <select wire:model.live="perPage" id="page-count" class="form-control">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            <label for="page-count">Results per page</label>
        </div>

        {{ $collection->links() }}
    </div>
@endif
