<?php

namespace Glint\Glint\Action;

use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    public function delete(Model $record)
    {
        $record->delete();
    }

    public function render($record)
    {
        return view('components.glint.row-actions.delete-action', [
            'record' => $record,
            'action' => $this,
        ]);
    }
}
