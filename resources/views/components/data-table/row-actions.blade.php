@props(['rowActions', 'item'])

@php
    $collection = collect($rowActions);
    $singleAction = $collection->count() === 1;
    
    $hide = $collection->every(fn($action) => !$action->isVisible($item));
    
    $rowAction = null;
    if ($singleAction && !$hide) {
        $rowAction = $collection->first(fn($action) => $action->isVisible($item));
    }
@endphp

<td class="td-actions">
    @if (!$hide)
        @if ($singleAction && $rowAction)
            {{-- Single Action --}}
            <div class="single-action">
                {{ $rowAction->render($item) }}
            </div>
        @else
            {{-- Actions List --}}
            <div x-data="{ showActions: false, close() { this.showActions = false } }" x-id="['row-actions']" class="data-table-actions"
                @focusin.window="! $root.contains($event.target) && close()"@keydown.escape.window="close()">
                <button @click.prevent="showActions = !showActions" type="button" x-ref="actionsButton"
                    :id="$id('row-actions', 'button')" :aria-controls="$id('row-actions')" :aria-expanded="showActions"
                    :class="{ 'active': showActions }" class="action-button">
                    <x-heroicon-s-ellipsis-horizontal x-show="!showActions" class="icon" />
                    <x-heroicon-s-x-mark x-cloak x-show="showActions" class="icon" />
                    <span class="visually-hidden">Available Actions</span>
                </button>

                <div x-anchor.bottom-end.offset.4="$refs.actionsButton" x-show="showActions" :id="$id('row-actions')"
                    :aria-labelledby="$id('row-actions', 'button')" @click.outside="close()" x-transition x-cloak>
                    <ul class="actions-list">
                        @foreach ($rowActions as $i => $action)
                            @if ($action->isVisible($item))
                                <li class="action-item">
                                    {{ $action->render($item) }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endif
</td>
