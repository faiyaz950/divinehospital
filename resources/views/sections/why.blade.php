@php($why = site('common.why'))
<section class="section why" id="why" aria-labelledby="why-title">
    <div class="container why__grid">
        <div class="why__intro">
            <p class="eyebrow eyebrow--light" data-reveal>{{ $why['eyebrow'] }}</p>
            <h2 class="h2" id="why-title" data-reveal>{{ site()->format($why['title']) }}</h2>
            <p class="lead" data-reveal>{{ $why['lead'] }}</p>
            <div class="btn-row" data-reveal>
                <a class="btn btn--gold btn--lg" href="{{ route('contact') }}#appointment" data-magnetic><x-icon name="calendar-check" /> {{ $why['button'] }}</a>
            </div>
        </div>

        <div class="why__cards">
            @foreach ($why['reasons'] as $reason)
                <article class="why-card spotlight spotlight--dark" data-reveal="scale" style="--i: {{ $loop->index }}">
                    <span class="why-card__num" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="why-card__icon"><x-icon :name="$reason['icon']" /></span>
                    <h3>{{ $reason['title'] }}</h3>
                    <p>{{ $reason['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
