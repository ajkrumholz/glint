<?php

namespace Glint\Glint;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

abstract class CollectedDataTable extends DataTable
{
    public $stored_collection;

    public function mount()
    {
        $this->stored_collection = $this->getCollection();

        parent::mount();
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    protected function getQuery()
    {
    }

    protected function buildQuery()
    {
        $collection = $this->getCollection() ?? $this->stored_collection;

        /* if (! empty($this->getFilters)) */
        /* { */
        /*     $this->filterBy($collection); */
        /* } */

        if (
            ! empty($this->getSearchableColumns()) &&
            ! empty($this->search)
        ) {
            $collection = $this->searchBy($collection);
        }

        /* if ($this->sortColumn) */
        /* { */
        /*     $this->addSortToQuery($collection); */
        /* } */

        return $collection;
    }

    protected function searchBy($collection)
    {
        $searchable_attrs = array_map(
            fn ($a) => $a->attribute, $this->getSearchableColumns()
        );
        $criteria = $this->search;

        return $collection->filter(function ($item) use ($searchable_attrs, $criteria) {
            foreach ($searchable_attrs as $attr) {
                $attr_val = strtolower($item[$attr]);
                if (strpos($attr_val, strtolower($criteria)) !== false) {
                    return true;
                }
            }

            return false;
        });

    }

    protected function build()
    {
        $collection = $this->buildQuery();

        return $this->paginate($collection, $this->perPage);
    }

    abstract protected function getCollection();
}
