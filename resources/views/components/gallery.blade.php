@props(['photos' => [], 'allLabel' => null])
@php
    $items = collect($photos)
        ->map(function (array $photo): ?array {
            $largest = collect([1600, 1024, 640, 560])->first(fn ($w) => $photo['image'] && is_file(public_path("images/{$photo['image']}-{$w}.webp")));

            return $largest ? $photo + [
                'full' => asset("images/{$photo['image']}-{$largest}.webp"),
                'group' => Str::slug($photo['category'] ?? ''),
            ] : null;
        })
        ->filter()
        ->values();

    $groups = $allLabel ? $items->pluck('category')->filter()->unique()->values() : collect();
@endphp

@if ($items->isNotEmpty())
    @if ($groups->count() > 1)
        <div class="spec-nav gallery-filter" role="group" aria-label="Filter photos" data-gallery-filter>
            <button type="button" aria-pressed="true" data-filter="">{{ $allLabel }}</button>
            @foreach ($groups as $group)
                <button type="button" aria-pressed="false" data-filter="{{ Str::slug($group) }}">{{ $group }}</button>
            @endforeach
        </div>
    @endif

    <div {{ $attributes->class('gallery') }} data-gallery>
        @foreach ($items as $item)
            <button class="gallery__item" type="button" data-gallery-item data-group="{{ $item['group'] }}" data-full="{{ $item['full'] }}" data-caption="{{ $item['caption'] }}" data-reveal="scale" style="--i: {{ $loop->index % 4 }}">
                <x-picture :name="$item['image']" :alt="$item['caption']" sizes="(min-width: 900px) 25vw, 50vw" />
                <span class="gallery__cap"><x-icon name="zoom" /> {{ $item['caption'] }}</span>
            </button>
        @endforeach
    </div>

    <dialog class="lightbox" data-lightbox aria-label="Photo viewer">
        <button class="lightbox__btn lightbox__close" type="button" data-lightbox-close aria-label="Close"><x-icon name="x" /></button>
        <button class="lightbox__btn lightbox__prev" type="button" data-lightbox-prev aria-label="Previous photo"><x-icon name="chevron-left" /></button>
        <figure class="lightbox__figure">
            <img src="" alt="" data-lightbox-img>
            <figcaption data-lightbox-caption></figcaption>
        </figure>
        <button class="lightbox__btn lightbox__next" type="button" data-lightbox-next aria-label="Next photo"><x-icon name="chevron-right" /></button>
    </dialog>
@endif
