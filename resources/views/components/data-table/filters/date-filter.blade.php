@php
    $start = 'filters.' . $filter->attribute . '.start';
    $end = 'filters.' . $filter->attribute . '.end';
@endphp

<fieldset class="flow">
    <legend>{{ $filter->label }}</legend>
    <div class="form-item">
        <label for="{{ 'filters.' . $filter->attribute . '.start' }}">Start Date</label>
        <input type="date" id="{{ 'filters.' . $filter->attribute . '.start' }}" wire:model.live="{{ 'filters.' . $filter->attribute . '.start' }}" />
    </div>

    <div class="form-item">
        <label for="{{ 'filters.' . $filter->attribute . '.end' }}">End Date</label>
        <input type="date" id="{{ 'filters.' . $filter->attribute . '.end' }}" wire:model.live="{{ 'filters.' . $filter->attribute . '.end' }}" />
    </div>
</fieldset>
