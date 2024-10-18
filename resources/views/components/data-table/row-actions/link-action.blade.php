<a href="{{ $action->getLink($record) }}"
  @if ($action->opensInNewWindow()) target="_blank" @endif>
  {{ $action->getLabel($record) }}
</a>