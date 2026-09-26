@php($block = site('home.facilities'))
<section class="section facilities" id="facilities" aria-labelledby="facilities-title">
    <div class="container">
        <x-section-head class="section-head--split" :eyebrow="$block['eyebrow']" :lead="$block['lead']">
            <x-slot:title><span id="facilities-title">{{ site()->format($block['title']) }}</span></x-slot:title>
            @if ($block['button'])
                <a class="btn btn--outline" href="{{ route('facilities') }}">{{ $block['button'] }} <x-icon name="arrow-right" /></a>
            @endif
        </x-section-head>

        <div class="bento">
            @foreach (site('facilities.list.items') as $facility)
                <article class="bento__item bento__item--{{ $facility['key'] }}" data-reveal="clip" style="--i: {{ $loop->index }}">
                    <x-picture class="bento__img" :name="$facility['image']" :alt="$facility['alt']" sizes="(min-width: 900px) 66vw, 100vw" />
                    <div class="bento__body">
                        <span class="chip-glass"><x-icon :name="$facility['icon']" /> {{ $facility['tag'] }}</span>
                        <h3>{{ $facility['title'] }}</h3>
                        <p>{{ $facility['text'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
