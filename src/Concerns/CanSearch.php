<?php

namespace Glint\Glint\Concerns;

trait CanSearch
{
    public bool $searchable = false;

    public array $search_columns = [];

    private string $table_name;

    private string $attr;

    private string $relation;

    public function searchable(array $search_columns = [], bool $searchable = true): static
    {
        $this->searchable = $searchable;
        $this->search_columns = $search_columns;

        return $this;
    }

    public function buildSearchQuery($search, $query, string $table_name)
    {
        $attr_array = explode('.', $this->attribute);

        $this->attr = array_pop($attr_array);
        $this->relation = implode('.', $attr_array);
        $this->table_name = $table_name;

        if (empty($this->relation)) {
            $this->basicSearch($search, $query);
        } else {
            $this->relationSearch($search, $query);
        }
    }

    private function basicSearch($search, $query)
    {
        if (! empty($this->search_columns)) {
            foreach ($this->search_columns as $i => $search_column) {
                $search_column = $this->table_name.'.'.$search_column;
                $i === 0 ? $query->where($search_column, 'like', '%'.$search.'%') :
                    $query->orWhere($search_column, 'like', '%'.$search.'%');
            }
        } else {
            $column = $this->table_name.'.'.$this->attr;
            $query->orWhere($column, 'like', '%'.$search.'%');
        }
    }

    private function relationSearch($search, $query)
    {
        $query->orWhereHas($this->relation, function ($query) use ($search) {
            if (! empty($this->search_columns)) {
                foreach ($this->search_columns as $i => $search_column) {
                    $i === 0 ? $query->where($search_column, 'like', '%'.$search.'%') :
                        $query->orWhere($search_column, 'like', '%'.$search.'%');
                }
            } else {
                $query->where($this->attr, 'like', '%'.$search.'%');
            }
        });
    }
}
