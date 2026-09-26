@php($block = site('home.services'))
<section class="section services" id="services" aria-labelledby="services-title">
    <div class="container">
        <x-section-head :eyebrow="$block['eyebrow']" align="center" :lead="$block['lead']">
            <x-slot:title><span id="services-title">{{ site()->format($block['title']) }}</span></x-slot:title>
        </x-section-head>

        <div class="spec-grid">
            @foreach (site('specialities.list.items') as $speciality)
                <article @class(['spec-card', 'spotlight', 'spec-card--featured spotlight--dark' => $loop->iteration === 2]) data-reveal="scale" style="--i: {{ $loop->index }}">
                    <div class="spec-card__head">
                        <span class="spec-card__icon"><x-icon :name="$speciality['icon']" /></span>
                        <span class="spec-card__num" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                    </div>
                    <h3 class="spec-card__title">{{ $speciality['title'] }}</h3>
                    <p class="spec-card__summary">{{ $speciality['summary'] }}</p>
                    <ul class="spec-card__list">
                        @foreach ($speciality['services'] as $service)
                            <li>
                                <x-icon name="check-circle" />
                                <div>
                                    <h4>{{ $service['title'] }}</h4>
                                    <p>{{ $service['text'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a class="link-arrow" href="{{ route('services') }}#{{ $speciality['key'] }}">
                        {{ site()->format($block['link_label'], ['name' => e($speciality['short'])]) }} <x-icon name="arrow-right" />
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
