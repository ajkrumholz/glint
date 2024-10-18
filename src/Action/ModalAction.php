<?php

namespace Glint\Glint\Action;

class ModalAction extends Action
{
    private string $component;

    private array $data = [];

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function data(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render($record)
    {
        return view('components.data-table.row-actions.modal-action', [
            'record' => $record,
            'action' => $this,
        ]);
    }
}
