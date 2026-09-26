@php($block = site('common.testimonials'))
<section class="section testimonials" aria-labelledby="testimonials-title">
    <div class="container">
        <x-section-head :eyebrow="$block['eyebrow']" align="center">
            <x-slot:title><span id="testimonials-title">{{ site()->format($block['title']) }}</span></x-slot:title>
        </x-section-head>

        <div class="testimonial-grid">
            @foreach ($block['reviews'] as $review)
                <figure class="testimonial spotlight" data-reveal="scale" style="--i: {{ $loop->index }}">
                    <x-icon name="quote" class="testimonial__mark" />
                    <blockquote><p>{{ $review['quote'] }}</p></blockquote>
                    <figcaption>
                        <span class="avatar avatar--sm" aria-hidden="true">{{ mb_substr($review['name'], 0, 1) }}</span>
                        <span><strong>{{ $review['name'] }}</strong><span>{{ $review['context'] }}</span></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        @if (site('clinic.map.reviews_url'))
            <p class="review-link"><a class="link-arrow" href="{{ site('clinic.map.reviews_url') }}" target="_blank" rel="noopener">{{ $block['link_label'] }} <x-icon name="arrow-up-right" /></a></p>
        @endif

        @if ($block['note'])
            <p class="review-note">{{ $block['note'] }}</p>
        @endif
    </div>
</section>
