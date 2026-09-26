@php
    $hero = site('patient.hero');
    $visit = site('patient.visit');
    $procedure = site('patient.procedure');
    $faq = site('patient.faq');
@endphp
<x-layout :title="site('patient.seo.title')" :description="site('patient.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
        <div class="btn-row">
            <a class="btn btn--gold btn--lg" href="{{ route('contact') }}#appointment"><x-icon name="calendar-check" /> {{ $hero['primary_button'] }}</a>
            @if ($hero['secondary_button'] && $faq['items'])
                <a class="btn btn--outline btn--lg" href="#faq">{{ $hero['secondary_button'] }} <x-icon name="arrow-right" /></a>
            @endif
        </div>
    </x-page-hero>

    @if ($steps = site('patient.steps.items'))
        <section class="section section--flush-top" aria-labelledby="steps-title">
            <div class="container">
                <h2 class="sr-only" id="steps-title">How your visit works</h2>
                <ol class="steps">
                    @foreach ($steps as $step)
                        <li class="step spotlight" data-reveal="scale" style="--i: {{ $loop->index }}">
                            <span class="step__num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    <section class="section section--alt" aria-label="Timings and what to bring">
        <div class="container info-split">
            <article class="panel" data-reveal="left">
                <h2 class="panel__title"><span class="icon-bubble"><x-icon name="clipboard" /></span> {{ $visit['bring_title'] }}</h2>
                <ul class="checklist">
                    @foreach ($visit['bring'] as $item)
                        <li><span class="tick tick--sm"><x-icon name="check" /></span>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>

            <article class="panel panel--dark" data-reveal="right" style="--i:1">
                <h2 class="panel__title"><span class="icon-bubble icon-bubble--gold"><x-icon name="clock" /></span> {{ $visit['timings_title'] }}</h2>
                <div class="hours-list" data-open-status>
                    <p class="hours-list__status"><span class="status-dot" aria-hidden="true"></span> <span data-open-label>OPD</span> <span data-open-detail></span></p>
                    <dl>
                        @foreach ($clinic->sessions() as $session)
                            <div><dt>{{ site('clinic.hours.days_label') }} · {{ $session['label'] }}</dt><dd>{{ $session['range'] }}</dd></div>
                        @endforeach
                        <div><dt>{{ site('clinic.hours.closed_label') }}</dt><dd>{{ site('clinic.hours.closed_note') }}</dd></div>
                    </dl>
                </div>
                <div class="btn-row">
                    <a class="btn btn--gold" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $clinic->phone() }}</a>
                    <a class="btn btn--outline-light" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> WhatsApp</a>
                </div>
            </article>
        </div>
    </section>

    <section class="section" aria-labelledby="procedure-title">
        <div class="container">
            <x-section-head :eyebrow="$procedure['eyebrow']" :lead="$procedure['lead']">
                <x-slot:title><span id="procedure-title">{{ site()->format($procedure['title']) }}</span></x-slot:title>
            </x-section-head>
            <div class="info-split">
                <article class="panel panel--tint" data-reveal>
                    <h3 class="panel__title"><span class="icon-bubble"><x-icon name="clipboard" /></span> {{ $procedure['before_title'] }}</h3>
                    <ul class="checklist">
                        @foreach ($procedure['before'] as $item)
                            <li><span class="tick tick--sm"><x-icon name="check" /></span>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
                <article class="panel panel--tint" data-reveal style="--i:1">
                    <h3 class="panel__title"><span class="icon-bubble"><x-icon name="heart" /></span> {{ $procedure['after_title'] }}</h3>
                    <ul class="checklist">
                        @foreach ($procedure['after'] as $item)
                            <li><span class="tick tick--sm"><x-icon name="check" /></span>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
            @if ($procedure['emergency'])
                <div class="notice" data-reveal>
                    <x-icon name="alert" />
                    <p>{{ site()->format($procedure['emergency'], ['phone' => '<a href="'.e($clinic->phoneHref()).'">'.e($clinic->phone()).'</a>']) }}</p>
                </div>
            @endif
        </div>
    </section>

    @if ($faq['items'])
        <section class="section section--alt" id="faq" aria-labelledby="faq-title">
            <div class="container">
                <x-section-head :eyebrow="$faq['eyebrow']" align="center">
                    <x-slot:title><span id="faq-title">{{ site()->format($faq['title']) }}</span></x-slot:title>
                </x-section-head>
                <div class="faq">
                    @foreach ($faq['items'] as $item)
                        <details data-reveal style="--i: {{ $loop->index }}" @if ($loop->first) open @endif>
                            <summary>{{ $item['q'] }}<span class="faq__icon" aria-hidden="true"><x-icon name="plus" /></span></summary>
                            <div class="faq__answer"><p>{{ $item['a'] }}</p></div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
