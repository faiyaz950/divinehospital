@php
    $type = $field['type'];
    $segments = explode('.', $path);
    $bracket = fn (string $root) => $root.'['.implode('][', $segments).']';
    $inputName = $bracket('fields');
    $id = 'f-'.preg_replace('/[^a-z0-9]+/i', '-', $path);
    $errorKey = 'fields.'.$path;
    $hasOld = ! $isTemplate && session()->hasOldInput();
    $current = $hasOld ? old($errorKey) : $value;
    $error = $isTemplate ? null : ($errors->first($errorKey) ?: $errors->first('uploads.'.$path));
    $label = $field['label'] ?? Str::headline(last($segments));
    $full = in_array($type, ['textarea', 'list', 'repeater', 'image', 'picture', 'days'], true) || ($field['full'] ?? false);
@endphp

@if ($type === 'repeater')
    @php
        $rows = is_array($current) ? $current : [];
        $placeholder = "__I{$depth}__";
        $rowDefaults = collect($field['fields'])->map(fn ($sub) => $sub['default'] ?? null)->all();
    @endphp
    <div class="a-field a-field--full a-repeater" data-repeater data-placeholder="{{ $placeholder }}" data-max="{{ $field['max'] ?? 60 }}">
        <div class="a-repeater__head">
            <span class="a-label">{{ $label }}</span>
            <span class="a-muted a-small" data-count>{{ count($rows) }} {{ Str::plural('item', count($rows)) }}</span>
        </div>
        @if ($error)
            <p class="a-error">{{ $error }}</p>
        @endif
        <div class="a-repeater__rows" data-rows>
            @foreach ($rows as $index => $row)
                @include('admin.content.row', ['row' => (array) $row, 'index' => $index, 'open' => ! $isTemplate && $errors->has("fields.{$path}.{$index}.*")])
            @endforeach
        </div>
        <template data-template>
            @include('admin.content.row', ['row' => $rowDefaults, 'index' => $placeholder, 'open' => true, 'isTemplate' => true])
        </template>
        <button class="a-btn a-btn--soft a-btn--sm" type="button" data-add><x-icon name="plus" /> {{ $field['add_label'] ?? 'Add item' }}</button>
    </div>
@else
    <div @class(['a-field', 'a-field--full' => $full, 'has-error' => $error])>
        @if ($type === 'toggle')
            <input type="hidden" name="{{ $inputName }}" value="0">
            <label class="a-switch">
                <input type="checkbox" id="{{ $id }}" name="{{ $inputName }}" value="1" @checked($current)>
                <span class="a-switch__track" aria-hidden="true"></span>
                <span>{{ $label }}</span>
            </label>
        @else
            <label class="a-label" for="{{ $id }}">
                {{ $label }}
                @if ($field['required'] ?? false)
                    <span class="a-req" aria-hidden="true">*</span>
                @endif
            </label>

            @switch($type)
                @case('textarea')
                @case('list')
                    <textarea class="a-input" id="{{ $id }}" name="{{ $inputName }}" rows="{{ $field['rows'] ?? ($type === 'list' ? 5 : 3) }}" @required($field['required'] ?? false)>{{ is_array($current) ? implode("\n", $current) : $current }}</textarea>
                    @break

                @case('select')
                    <select class="a-input" id="{{ $id }}" name="{{ $inputName }}" @required($field['required'] ?? false) data-title-source>
                        @unless ($field['required'] ?? false)
                            <option value="">—</option>
                        @endunless
                        @foreach ($field['options'] as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                    @break

                @case('icon')
                    <div class="a-icon-select">
                        <span class="a-icon-select__preview" data-icon-preview><x-icon :name="$current ?: 'info'" /></span>
                        <select class="a-input" id="{{ $id }}" name="{{ $inputName }}" data-icon-select>
                            @foreach ($icons as $icon)
                                <option value="{{ $icon }}" @selected($current === $icon)>{{ Str::headline($icon) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('days')
                    <div class="a-days">
                        @foreach ([1 => 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day => $dayLabel)
                            <label class="a-chip-check">
                                <input type="checkbox" name="{{ $inputName }}[]" value="{{ $day }}" @checked(in_array($day, array_map('intval', (array) $current), true))>
                                <span>{{ $dayLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('image')
                @case('picture')
                    @php
                        $preview = null;
                        if ($current && $type === 'picture') {
                            $width = collect([640, 560, 1024, 1600])->first(fn ($w) => is_file(public_path("images/{$current}-{$w}.webp")));
                            $preview = $width ? asset("images/{$current}-{$width}.webp") : null;
                        } elseif ($current && is_file(public_path($current))) {
                            $preview = asset($current);
                        }
                    @endphp
                    <div @class(['a-upload', 'a-upload--wide' => $type === 'picture'])>
                        <div class="a-upload__preview" data-upload-preview>
                            @if ($preview)
                                <img src="{{ $preview }}" alt="">
                            @else
                                <span class="a-muted a-small">No image</span>
                            @endif
                        </div>
                        <div class="a-upload__controls">
                            <input type="hidden" name="{{ $inputName }}" value="{{ $current }}">
                            <input class="a-file" id="{{ $id }}" type="file" name="{{ $bracket('uploads') }}" accept="image/jpeg,image/png,image/webp,image/gif" data-upload-input>
                            <p class="a-hint">JPG, PNG or WebP up to 10 MB.{{ $type === 'picture' ? ' Resized automatically for phones and computers.' : '' }}</p>
                            @if ($current && ! ($field['required'] ?? false))
                                <label class="a-check a-small"><input type="checkbox" name="{{ $bracket('remove') }}" value="1"> Remove this image</label>
                            @endif
                        </div>
                    </div>
                    @break

                @default
                    <input
                        class="a-input"
                        id="{{ $id }}"
                        type="{{ match ($type) { 'email' => 'email', 'url' => 'url', 'time' => 'time', default => 'text' } }}"
                        name="{{ $inputName }}"
                        value="{{ $current }}"
                        @if (isset($field['max'])) maxlength="{{ $field['max'] }}" @endif
                        @required($field['required'] ?? false)
                        data-title-source
                    >
            @endswitch
        @endif

        @if (! empty($field['hint']))
            <p class="a-hint">{{ $field['hint'] }}</p>
        @endif
        @if ($error)
            <p class="a-error">{{ $error }}</p>
        @endif
    </div>
@endif
