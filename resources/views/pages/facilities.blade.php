@php
    $hero = site('facilities.hero');
    $list = site('facilities.list');
    $gallery = site('facilities.gallery');
@endphp
<x-layout :title="site('facilities.seo.title')" :description="site('facilities.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :image="$hero['image']" :image-alt="$hero['image_alt']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
    </x-page-hero>

    <section class="section section--flush-top" aria-label="Facilities">
        <div class="container">
            @foreach ($list['items'] as $facility)
                <article class="feature-row" id="{{ $facility['key'] }}">
                    <figure class="feature-row__media feature-row__media--{{ $facility['key'] }}" data-reveal="clip">
                        <x-picture :name="$facility['image']" :alt="$facility['alt']" sizes="(min-width: 960px) 600px, 100vw" />
                    </figure>
                    <div class="feature-row__body" data-reveal="{{ $loop->even ? 'left' : 'right' }}" style="--i:2">
                        <span class="spec-card__icon"><x-icon :name="$facility['icon']" /></span>
                        <p class="eyebrow">{{ $list['eyebrow'] }} {{ sprintf('%02d', $loop->iteration) }}@if ($facility['tag']) · {{ $facility['tag'] }}@endif</p>
                        <h2 class="h2 h2--sm">{{ $facility['title'] }}</h2>
                        <p class="lead">{{ $facility['text'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if ($gallery['photos'])
        <section class="section section--alt" aria-labelledby="gallery-title">
            <div class="container">
                <x-section-head :eyebrow="$gallery['eyebrow']" align="center" :lead="$gallery['lead']">
                    <x-slot:title><span id="gallery-title">{{ site()->format($gallery['title']) }}</span></x-slot:title>
                </x-section-head>
                <div class="gallery" data-gallery>
                    @foreach ($gallery['photos'] as $photo)
                        @php($largest = collect([1600, 1024, 640, 560])->first(fn ($w) => $photo['image'] && is_file(public_path("images/{$photo['image']}-{$w}.webp"))))
                        @continue(! $largest)
                        <button class="gallery__item" type="button" data-gallery-item data-full="{{ asset("images/{$photo['image']}-{$largest}.webp") }}" data-caption="{{ $photo['caption'] }}" data-reveal="scale" style="--i: {{ $loop->index % 4 }}">
                            <x-picture :name="$photo['image']" :alt="$photo['caption']" sizes="(min-width: 900px) 25vw, 50vw" />
                            <span class="gallery__cap"><x-icon name="zoom" /> {{ $photo['caption'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

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

    @include('sections.why')
</x-layout>
