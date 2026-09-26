@php
    $hero = site('services.hero');
    $blocks = site('services.blocks');
    $interests = site('services.interests');
    $specialities = site('specialities.list.items');
@endphp
<x-layout :title="site('services.seo.title')" :description="site('services.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :image="$hero['image']" :image-alt="$hero['image_alt']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
        <div class="btn-row">
            <a class="btn btn--gold btn--lg" href="{{ route('contact') }}#appointment"><x-icon name="calendar-check" /> {{ $hero['button'] }}</a>
        </div>
    </x-page-hero>

    <section class="section section--flush-top" aria-label="Treatments by speciality">
        <div class="container">
            <nav class="spec-nav" aria-label="Specialities" data-spec-nav>
                @foreach ($specialities as $speciality)
                    <a href="#{{ $speciality['key'] }}"><x-icon :name="$speciality['icon']" /> {{ $speciality['short'] }}</a>
                @endforeach
            </nav>

            @foreach ($specialities as $speciality)
                <div class="spec-block" id="{{ $speciality['key'] }}">
                    <div class="spec-block__intro" data-reveal="left">
                        <span class="spec-card__icon"><x-icon :name="$speciality['icon']" /></span>
                        <p class="eyebrow">{{ $blocks['eyebrow'] }} {{ sprintf('%02d', $loop->iteration) }}</p>
                        <h2 class="h2 h2--sm">{{ $speciality['title'] }}</h2>
                        <p class="lead">{{ $speciality['summary'] }}</p>
                        <a class="btn btn--teal" href="{{ route('contact', ['concern' => $speciality['key']]) }}#appointment"><x-icon name="calendar-check" /> {{ site()->format($blocks['book_label'], ['name' => e($speciality['short'])]) }}</a>
                    </div>
                    <div class="service-cards">
                        @foreach ($speciality['services'] as $service)
                            <article class="service-card spotlight" data-reveal="scale" style="--i: {{ $loop->index }}">
                                <span class="icon-bubble"><x-icon :name="$service['icon']" /></span>
                                <h3>{{ $service['title'] }}</h3>
                                <p>{{ $service['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @include('sections.marquee')

    <section class="section" aria-labelledby="interests-title">
        <div class="container">
            <x-section-head :eyebrow="$interests['eyebrow']" align="center" :lead="$interests['lead']">
                <x-slot:title><span id="interests-title">{{ site()->format($interests['title']) }}</span></x-slot:title>
            </x-section-head>
            <ul class="expertise expertise--grid">
                @foreach (site('doctor.bio.expertise') as $item)
                    <li data-reveal style="--i: {{ $loop->index }}">
                        <span class="icon-bubble"><x-icon :name="$item['icon']" /></span>
                        {{ $item['title'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    @include('sections.why')
</x-layout>
