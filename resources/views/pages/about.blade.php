@php($hero = site('about.hero'))
<x-layout :title="site('about.seo.title')" :description="site('about.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
        <div class="btn-row">
            <a class="btn btn--gold btn--lg" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $hero['call_button'] }}</a>
            @if ($hero['whatsapp_button'])
                <a class="btn btn--outline btn--lg" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> {{ $hero['whatsapp_button'] }}</a>
            @endif
        </div>
    </x-page-hero>

    @include('sections.about', ['full' => true])

    @if ($panels = site('about.panels.items'))
        <section class="section section--flush-top" aria-label="Qualifications and approach">
            <div class="container">
                <div class="card-row">
                    @foreach ($panels as $panel)
                        <article class="panel spotlight" data-reveal="scale" style="--i:{{ $loop->index }}">
                            <span class="icon-bubble"><x-icon :name="$panel['icon']" /></span>
                            <h3>{{ $panel['title'] }}</h3>
                            @if ($panel['text'])
                                <p>{{ $panel['text'] }}</p>
                            @endif
                            @if ($panel['points'])
                                <ul class="checklist">
                                    @foreach ($panel['points'] as $point)
                                        <li><span class="tick tick--sm"><x-icon name="check" /></span>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.why')
    @include('sections.testimonials')
    @include('sections.location')
</x-layout>
