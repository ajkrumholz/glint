<?php

namespace Glint\Glint;

use Glint\Glint\Filter\SelectFilter;
use Livewire\Component;

class Choices extends Component
{
    public string $label;

    public bool $multiple = false;

    public array $selections = [];

    public $value;

    public $field;

    public $options = [];

    public function mount(SelectFilter $filter)
    {
        $this->label = $filter->label;

        $this->multiple = $filter->isMultiple();

        if ($filter->default) {
            $this->selections = is_array($filter->default) ? $filter->default : [$filter->default];
        }

        $this->field = $filter->attribute;
        $this->options = $this->getOptions($filter->options);

        $this->dispatch('updateFilter', field: $this->field, selections: $this->selections);
    }

    protected function getOptions(array $options): array
    {
        foreach ($options as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
                'selected' => in_array($value, $this->selections),
            ];
        }

        return $result ?? [];
    }

    public function render()
    {
        return view('livewire.data-table.choices');
    }
}
