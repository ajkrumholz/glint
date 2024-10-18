@assets
    @vite(['resources/js/choices.js', 'resources/scss/choices.scss'])
@endassets

<div class="glint-choices filter-choices form-item" >
    <label for="filter-{{ $field }}">{{ $label }}</label>
    <div id="filter-{{ $field }}" class="filter-choices form-item" x-data="{
        multiple: @entangle('multiple'),
        selections: @entangle('selections').live,
        options: {{ json_encode($options) }},
        init() {
            this.$nextTick(() => {
                let choices = new Choices(this.$refs.select, {
                    removeItems: true,
                    removeItemButton: this.multiple ? true : false,
                    allowHTML: false,
                    shouldSort: false,
                    labelId: '{{ $field }}',
                });
    
                let refreshChoices = () => {
                    let selection = this.selections;
    
                    choices.clearStore();
                    choices.setChoices(this.options.map(option => ({
                        value: option.value,
                        label: option.label,
                        selected: selection.includes(option.value)
                    })));
                }
    
                refreshChoices();
    
                this.$refs.select.addEventListener('change', () => {
                    values = choices.getValue(true);
                    if (!Array.isArray(values)) {
                        values = [values];
                    }
                    this.selections = values;
                    $wire.dispatch('updateFilter', { field: '{{ $field }}', selections: this.selections });
                })
    
                this.$watch('selections', () => refreshChoices())
                this.$watch('options', () => refreshChoices())
            });
        }
    }" wire:ignore>
        <select x-ref="select" @if ($multiple) multiple @endif></select>
    </div>
</div>
