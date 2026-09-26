{{-- Responsive WebP image from public/images/{name}-{width}.webp (admin uploads live in images/uploads) --}}
@props(['name', 'alt' => '', 'sizes' => '100vw', 'eager' => false])
@php
    $widths = collect([560, 640, 1024, 1600])
        ->filter(fn ($w) => $name && is_file(public_path("images/{$name}-{$w}.webp")))
        ->values();
@endphp
@if ($widths->isNotEmpty())
    @php
        [$width, $height] = getimagesize(public_path("images/{$name}-{$widths->last()}.webp"));
        $srcset = $widths->map(fn ($w) => asset("images/{$name}-{$w}.webp")." {$w}w")->implode(', ');
        $fallback = $widths->count() > 1 ? $widths[1] : $widths[0];
    @endphp
    <img
        src="{{ asset("images/{$name}-{$fallback}.webp") }}"
        srcset="{{ $srcset }}"
        sizes="{{ $sizes }}"
        width="{{ $width }}"
        height="{{ $height }}"
        alt="{{ $alt }}"
        decoding="async"
        @if ($eager) fetchpriority="high" @else loading="lazy" @endif
        {{ $attributes }}
    >
@endif
