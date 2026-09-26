@php
    $titleField = $field['item_label'] ?? array_key_first($field['fields']);
    $titleValue = $row[$titleField] ?? '';
    if (($field['fields'][$titleField]['type'] ?? null) === 'select') {
        $titleValue = $field['fields'][$titleField]['options'][$titleValue] ?? $titleValue;
    }
@endphp
<details class="a-row" data-row data-title-field="{{ $titleField }}" @if ($open) open @endif>
    <summary class="a-row__head">
        <span class="a-row__chevron" aria-hidden="true"><x-icon name="chevron-right" /></span>
        <span class="a-row__title" data-row-title>{{ is_string($titleValue) && $titleValue !== '' ? Str::limit($titleValue, 80) : 'New item' }}</span>
        <span class="a-row__actions">
            <button class="a-icon-btn" type="button" data-move="up" title="Move up" aria-label="Move up"><x-icon name="chevron-left" class="a-rot-90" /></button>
            <button class="a-icon-btn" type="button" data-move="down" title="Move down" aria-label="Move down"><x-icon name="chevron-right" class="a-rot-90" /></button>
            <button class="a-icon-btn a-icon-btn--danger" type="button" data-remove title="Remove" aria-label="Remove"><x-icon name="x" /></button>
        </span>
    </summary>
    <div class="a-row__body a-grid">
        @foreach ($field['fields'] as $subName => $subField)
            @include('admin.content.field', [
                'field' => $subField,
                'path' => "{$path}.{$index}.{$subName}",
                'value' => $row[$subName] ?? null,
                'depth' => $depth + 1,
                'isTemplate' => $isTemplate,
            ])
        @endforeach
    </div>
</details>
