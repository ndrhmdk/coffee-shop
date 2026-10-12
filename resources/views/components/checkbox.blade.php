@isset($label)
    <label class="inline-flex items-center gap-2 text-on-surface-600">
@endisset

<input
    {{ $attributes->merge([
        'class' => 'size-4 shrink-0 rounded border border-surface-300 bg-surface accent-primary-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600' .
            ($errors->has($name ?? null) ? ' border-red-500' : ''),
    ]) }}
    type="checkbox"
    @if (isset($model)) wire:model="{{ $model }}" @endif
    @if (isset($name)) name="{{ $name }}" @endif
    @if (isset($readonly)) disabled @endif
    @if (isset($selected) && $selected === true) checked @endif
    @if (isset($value)) value="{{ $value }}" @endif
/>

@isset($label)
    <span>{{ $label }}</span>
    </label>
@endisset

@error($name ?? '')
    <span class="text-sm text-red-500">{{ $message }}</span>
@enderror
