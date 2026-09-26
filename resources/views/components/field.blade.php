@props(['name', 'label', 'type' => 'text', 'required' => false, 'hint' => null, 'full' => false])
@php
    $id = 'f-'.$name;
    $error = $errors->first($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $error ? "{$id}-error" : null])->filter()->implode(' ');
@endphp
<div @class(['field', 'field--full' => $full])>
    <label for="{{ $id }}">{{ $label }}@if ($required)<span class="req" aria-hidden="true"> *</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->class('input') }} @required($required) @if ($error) aria-invalid="true" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>{{ old($name) }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" {{ $attributes->class('input') }} @required($required) @if ($error) aria-invalid="true" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
    @endif
    @if ($hint)
        <p class="field__hint" id="{{ $id }}-hint">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="field__error" id="{{ $id }}-error"><x-icon name="alert" /> {{ $error }}</p>
    @endif
</div>
