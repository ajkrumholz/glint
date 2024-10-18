<?php

namespace Glint\Glint;

use Glint\Glint\Filter\DateFilter;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    public array $perPageOptions = [10, 25, 50, 100];

    public int $perPage = 10;

    public ?string $sortColumn = null;

    public string $emptyStateMessage = 'No records found.';

    public bool $sortAsc = true;

    public string $search = '';

    public bool $showSearch;

    /* public array $toggleColumns = []; */

    public array $toggledColumnAttr = [];

    public array $filters = [];

    protected array $searchable_columns = [];

    private Model $model;

    public function mount()
    {
        $this->perPage = $this->perPageOptions[0];

        $this->searchable_columns = $this->getSearchableColumns();
        $this->setFilterDefaults();

        $this->showSearch = ! empty($this->searchable_columns);
    }

    // ---------------- Abstract Functions ---------------- //
    /**
     * These functions must be implemented in the child class
     */
    abstract protected function getColumns();

    abstract protected function getQuery();

    // ---------------- Public Functions ---------------- //
    /**
     * These functions are called from the view
     */
    public function sortBy($attribute)
    {
        if (! $attribute) {
            return;
        }

        if ($this->sortColumn === $attribute) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortColumn = $attribute;
    }

    public function toggleColumn($column_attr)
    {
        if (in_array($column_attr, $this->toggledColumnAttr)) {
            // attributed to https://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
            if (($key = array_search($column_attr, $this->toggledColumnAttr)) !== false) {
                unset($this->toggledColumnAttr[$key]);
            }

        } else {
            $this->toggledColumnAttr[] = $column_attr;
        }

    }

    #[On('updateFilter')]
    public function setFilter($field, $selections)
    {
        $this->goToPage(1);

        if (array_key_exists($field, $this->filters)) {
            $this->filters[$field] = $selections;
        }
    }

    // ---------------- Protected Functions ---------------- //
    /**
     * These functions can be overridden in the child class
     */
    protected function getFilters(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getRowActions(): array
    {
        return [
            //
        ];
    }

    protected function getSearchableColumns(): array
    {
        return collect($this->getColumns())
            ->where('searchable', true)
            ->toArray();
    }

    protected function getToggleColumns(): array
    {
        $toggledColumns = collect($this->getColumns())
            ->filter(fn ($column) => $column->isToggleable());

        foreach ($toggledColumns as $column) {
            if (in_array($column->getAttribute(), $this->toggledColumnAttr)) {
                $column->toggledVisible = ! $column->isVisible();
            }
        }

        return $toggledColumns->toArray();
    }

    protected function getSelectFields(): array
    {
        return [
            //
        ];
    }

    /**
     * buildQuery() is used internally, and should not be overriden in the child class, but is used by children for table exports.
     */
    protected function buildQuery()
    {
        $query = $this->getQuery();

        // BD: maybe pointless? But I want to have to call getQuery everytime I
        // want the base model
        $this->model = $query->getModel();

        if (! empty($this->getFilters())) {
            $this->filterBy($query);
        }

        if (! empty($this->getSearchableColumns())) {
            $this->searchBy($query);
        }

        if ($this->sortColumn) {
            $this->addSortToQuery($query);
        }

        $table_name = $this->model->getTable();

        if (empty($query->getQuery()->getColumns())) {
            $query = $query->select($table_name.'.*');
        }

        return $query;
    }

    /**
     * Add joins and sort to Builder query
     *
     * add sort to query that will be passed to a paginator and likely has
     * eager loading. This method automatically adds in the necessary join.
     *
     * We have found that even with a `with` statement, we need the joins in
     * order for the orderBy to work. We can't use a sortBy because of the
     * paginator.
     *
     * We expect the chain of relationships in sortColumn to be in dot notation
     * with the final string being the attribute.
     */
    private function addSortToQuery($query)
    {
        $model = $this->getModel();
        $relationships = explode('.', $this->sortColumn);
        $attribute = array_pop($relationships);

        if (filled($relationships)) {
            foreach ($relationships as $relationship) {

                $next_relation_model_name = $this->getRelationshipName($relationship);
                $model = $model->{$next_relation_model_name}();
                $this->addJoinToQuery($query, $model);
            }
        }

        $model = filled($relationships) ? $model->getRelated() : $model;
        if (! $model instanceof Model) {
            $msg = "There was an error sorting by {$this->sortColumn}";
            throw new \Exception($msg);
        }

        $sort_column = $model->getTable().'.'.$attribute;
        $query->orderBy($sort_column, $this->sortAsc ? 'asc' : 'desc');
    }

    /**
     * Adds a join to a query given a related model
     */
    private function addJoinToQuery(Builder $query, $related_model): void
    {
        $foreign_key = $related_model->getQualifiedForeignKeyName();
        $db_table_name = $related_model->getRelated()->getTable();

        try {
            $owner_key = $related_model->getQualifiedOwnerKeyName();
        } catch (\BadMethodCallException $e) {
            $owner_key = $related_model->getQualifiedParentKeyName();
        }

        $query->leftJoin($db_table_name, $owner_key, '=', $foreign_key);
    }

    public function getModel(): Model
    {
        $this->model ??= $this->getQuery()->getModel();

        return $this->model;
    }

    protected function getRelationshipName($relation)
    {
        return strtolower($relation);
    }

    // ---------------- Private Functions ---------------- //
    /**
     * These functions are used internally
     */
    private function setFilterDefaults()
    {
        foreach ($this->getFilters() as $filter) {
            if (method_exists(get_class($filter), 'isMultiple') && $filter->isMultiple() && ! is_array($filter->default)) {
                $this->filters[$filter->attribute] = [$filter->default];
            } else {
                $this->filters[$filter->attribute] = $filter->default;
            }

            if (get_class($filter) === DateFilter::class) {
                $this->filters[$filter->attribute] = [
                    'start' => $filter->default_start ?? null,
                    'end' => $filter->default_end ?? null,
                ];
            }
        }
    }

    private function filterBy($query)
    {
        foreach ($this->getFilters() as $filter) {
            if ($filter->isVisible()) {
                $value = Arr::get($this->filters, $filter->attribute);
                $filter->processQuery($query, $value);
            }
        }
    }

    protected function searchBy($query)
    {
        $query->where(
            fn ($query) => empty($this->search) ?
                $query :
                $query->where(function ($query) {
                    foreach ($this->getSearchableColumns() as $column) {
                        $column->buildSearchQuery($this->search, $query, $this->getTableName());
                    }
                })
        );
    }

    protected function getTableName(): string
    {
        return $this->getQuery()->getModel()->getTable();
    }

    protected function build()
    {
        return $this->buildQuery()->paginate($this->perPage)->onEachSide(2);
    }

    // ---------------- Export Functions ---------------- //
    public function export()
    {
        $headers = $this->getExportHeaders();
        $columns = $this->getExportColumns();

        if (empty($columns)) {
            throw new \Exception('No columns found for table export - '.get_class($this));
        }

        $callback = $this->getExportCallback($columns);

        $this->dispatch('toast', type: 'success', message: 'Table export downloading.');

        return response()->stream($callback, 200, $headers);
    }

    private function getExportHeaders(): array
    {
        $filename = $this->getFormattedExportFilename();

        return [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Expires' => '0',
            'Pragma' => 'public',
        ];
    }

    /**
     * Override this function in a table to customize export filename
     * For example, if the name should change based on filters or other data,
     * you can override this function to return a dynamic name. It's not necessary
     * to add a .csv extension, as it is added automatically.
     */
    protected function getExportFilename(): string
    {
        return 'datatable-export';
    }

    /**
     * Formats the export filename with a timestamp. This can be overriden, but it's not
     * necessary. If you do choose to override, make sure to include the .csv extension.
     */
    protected function getFormattedExportFilename(): string
    {
        $filename = str_replace('.csv', '', $this->getExportFilename());

        return $filename.'-'.now()->format('m-d-Y-His').'.csv';
    }

    /**
     * The export callback relies on $column->getState() to get the data for each column.
     */
    private function getExportCallback(array $columns): Closure
    {
        return function () use ($columns) {
            $labels = $this->getExportLabels();

            $FH = fopen('php://output', 'w');
            fputcsv($FH, $labels);
            $this->buildQuery()->chunk(500, function ($records) use ($FH, $columns) {
                $records->each(function ($record) use ($FH, $columns) {
                    $data = array_map(fn ($c) => $c->getState($record), $columns);
                    fputcsv($FH, $data);
                });
            });
            fclose($FH);
        };
    }

    /**
     * Override this function in a table to customize export columns. This MUST return an array of Column objects.
     */
    protected function getExportColumns(): array
    {
        return $this->getColumns();
    }

    private function getExportLabels(): array
    {
        return collect($this->getExportColumns())
            ->map(fn ($column) => $column->label)
            ->toArray();
    }

    /**
     * Correctly set the toggle state for all columns
     *
     * On render, the toggle state of the columns does not natively persist.
     * Calls to `getColumns` in the `toggleColumn` callback generates a new
     * array of columns rather than altering the existing column instances.
     *
     * @optimize future optimization may want to ensure persistent column
     * instances.
     */
    private function getToggledColumns(): array
    {
        if (empty($this->toggledColumnAttr)) {
            return $this->getColumns();
        }

        $toggle = function ($column) {
            if (in_array($column->getAttribute(), $this->toggledColumnAttr)) {
                $column->toggledVisible = ! $column->isVisible();
            }

            return $column;

        };

        return array_map($toggle, $this->getColumns());
    }

    public function render()
    {
        return view(
            'livewire.glint.glint',
            [
                'collection' => $this->build(),
                'headerActions' => $this->getHeaderActions(),
                'rowActions' => $this->getRowActions(),
                'columns' => $this->getToggledColumns(),
                'tableFilters' => $this->getFilters(),
                'toggleColumns' => $this->getToggleColumns(),
            ]
        );
    }
}
