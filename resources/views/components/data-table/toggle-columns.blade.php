@props(['toggleColumns'])

<div
     x-data="{
         showToggles: false,
         close() {
             this.showToggles = false
         },
         toggleToggles() {
             this.showToggles = !this.showToggles
         }
     }"
     @focusin.window="! $refs.toggleMenu.contains($event.target) && close()"
     class="glint-toggles">

    <button class="glint-button toggle-menu-button"
            type="button"
            x-ref="toggleButton"
            :aria-expanded="showToggles"
            aria-controls="toggle-menu"
            @click.prevent="toggleToggles">
        <x-heroicon-s-ellipsis-horizontal class="icon" />
        <span class="visually-hidden">Toggles</span>
    </button>
    <div id="toggle-menu"
         x-ref="toggleMenu"
         x-anchor.bottom-end.offset.4="$refs.toggleButton"
         x-cloak
         x-show="showToggles"
         class="toggle-menu"
         @click.outside="close">
        <ul>
            @foreach ($toggleColumns as $column)
                <li>
                    <button wire:key="{{ "{$column->getAttribute()}-" . uniqid() }}"
                            class="toggle-button"
                            type="button"
                            :aria-checked="{{ $column->isVisible() ? 'true' : 'false' }}"
                            wire:click="toggleColumn('{{ $column->getAttribute() }}')">
                        <div class="toggle-button-switch"></div>
                        {{ $column->getLabel() }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
