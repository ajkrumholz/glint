<button type="button" wire:click.prevent="deleteRecord({{ $record->id }})">
  {{ $action->getLabel() }}
</button>