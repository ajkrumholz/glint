<div class="checkbox-group">
  <div class="checkbox">
      <input type="checkbox" id="{{ $filter->attribute }}"
          wire:model.live="filters.{{ $filter->attribute }}" />
      <label for="{{ $filter->attribute }}">{{ $filter->label }}</label>
  </div>
</div>