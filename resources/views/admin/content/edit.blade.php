<x-admin.layout :title="$screen['label']" :active="'content.'.$key">
    <div class="a-page-head">
        <div>
            @if (! empty($screen['description']))
                <p class="a-muted">{{ $screen['description'] }}</p>
            @endif
            <p class="a-muted a-small">
                @if ($customised)
                    Last saved {{ \Illuminate\Support\Carbon::parse($customised)->timezone(config('clinic.timezone'))->diffForHumans() }}.
                @else
                    Showing the original website content.
                @endif
                Tip: write <code>*word*</code> for the gold italic accent and <code>**word**</code> for bold where noted.
            </p>
        </div>
        @if (count($screen['sections']) > 1)
            <nav class="a-jump" aria-label="Sections">
                @foreach ($screen['sections'] as $sectionKey => $section)
                    <a href="#section-{{ $sectionKey }}">{{ $section['label'] }}</a>
                @endforeach
            </nav>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.content.update', $key) }}" enctype="multipart/form-data" class="a-editor" data-editor>
        @csrf
        @method('PUT')

        @foreach ($screen['sections'] as $sectionKey => $section)
            <section class="a-card" id="section-{{ $sectionKey }}">
                <header class="a-card__head">
                    <h2>{{ $section['label'] }}</h2>
                    @if (! empty($section['description']))
                        <p class="a-muted">{{ $section['description'] }}</p>
                    @endif
                </header>
                <div class="a-grid">
                    @foreach ($section['fields'] as $name => $field)
                        @include('admin.content.field', [
                            'field' => $field,
                            'path' => "{$sectionKey}.{$name}",
                            'value' => $values[$sectionKey][$name] ?? null,
                            'depth' => 0,
                            'isTemplate' => false,
                        ])
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="a-savebar">
            <p class="a-muted a-small" data-dirty-note hidden>You have unsaved changes.</p>
            @if (! empty($screen['route']))
                <a class="a-btn a-btn--ghost" href="{{ route($screen['route']) }}" target="_blank" rel="noopener"><x-icon name="arrow-up-right" /> View page</a>
            @endif
            <button class="a-btn a-btn--primary" type="submit" data-save><x-icon name="check" /> Save changes</button>
        </div>
    </form>

    @if ($customised)
        <form method="POST" action="{{ route('admin.content.reset', $key) }}" class="a-reset" data-confirm="Restore the original text and photos for “{{ $screen['label'] }}”? Your changes on this screen will be lost.">
            @csrf
            @method('DELETE')
            <button class="a-link a-link--danger" type="submit">Restore original content for this screen</button>
        </form>
    @endif

    <script type="application/json" id="admin-icons">@json(\App\Support\Icons::PATHS)</script>
</x-admin.layout>
