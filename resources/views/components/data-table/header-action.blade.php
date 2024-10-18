 @props(['action'])

 <div class="header-action">
     @switch(class_basename($action))
         @case('Action')
             <button class="glint-button"
                     @if ($action->getAction()) wire:click.prevent="{{ $action->getAction() }}" @endif>
                 {{ $action->getLabel() }}
             </button>
         @break

         @case('LinkAction')
             <a class="glint-button" href="{{ $action->getLink(null) }}">{{ $action->getLabel() }}</a>
         @break

         @case('ModalAction')
             <x-modal buttonText="{{ $action->getLabel() }}" buttonClasses="glint-button {{ $buttonClasses ?? '' }}">
                 <div class="flow">
                     @livewire($action->getComponent(), ['data' => $action->getData()], key(uniqid()))
                     <button type="button" class="button-secondary" @click="showModal = false">Cancel</button>
                 </div>
             </x-modal>
         @break

         @case('ExportAction')
             <button type="button" class="glint-button" wire:click.prevent="export">
                 {{ $action->getLabel() }}
             </button>
         @break
     @endswitch
 </div>
