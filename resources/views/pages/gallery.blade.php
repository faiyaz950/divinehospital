@php
    $hero = site('gallery.hero');
    $photos = site('gallery.photos');
@endphp
<x-layout :title="site('gallery.seo.title')" :description="site('gallery.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :image="$hero['image']" :image-alt="$hero['image_alt']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
    </x-page-hero>

    <section class="section section--alt" aria-labelledby="gallery-title">
        <div class="container">
            <x-section-head :eyebrow="$photos['eyebrow']" align="center" :lead="$photos['lead']">
                <x-slot:title><span id="gallery-title">{{ site()->format($photos['title']) }}</span></x-slot:title>
            </x-section-head>
            <x-gallery class="gallery--grid" :photos="$photos['items']" :all-label="$photos['all_label']" />
        </div>
    </section>
</x-layout>
