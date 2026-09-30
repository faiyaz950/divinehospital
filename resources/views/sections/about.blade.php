@php
    $full ??= false;
    $profile = site('doctor.profile');
    $bio = site('doctor.bio');
    $block = site('doctor.section');
    $photo = $clinic->doctorPhotoUrl();
@endphp
<section class="section about" id="about" aria-labelledby="about-title">
    <div class="container about__grid">
        <div @class(['about__media', 'about__media--photo' => $photo])>
            <div @class(['profile-card', 'profile-card--photo' => $photo]) data-reveal="clip" data-tilt>
                @if ($photo)
                    <img class="profile-card__photo" src="{{ $photo }}" alt="{{ $profile['photo_alt'] }}" width="776" height="950" loading="lazy">
                @else
                    <img class="profile-card__mark" src="{{ $clinic->logoUrl(white: true) }}" alt="" width="230" height="198" loading="lazy">
                    <span class="profile-card__initials" aria-hidden="true">{{ $profile['initials'] }}</span>
                @endif
                <div class="profile-card__body">
                    <p class="profile-card__name">{{ $profile['name'] }}</p>
                    <p class="profile-card__creds">{{ $profile['role'] }}</p>
                    @if ($profile['qualifications'])
                        <ul class="profile-card__chips">
                            @foreach ($profile['qualifications'] as $qualification)
                                <li>{{ $qualification }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            @unless ($photo)
                <figure class="about__inset" data-reveal="pop" style="--i:4">
                    <x-picture :name="$block['inset_image']" :alt="$block['inset_alt']" sizes="(min-width: 1024px) 260px, 45vw" />
                </figure>
            @endunless
        </div>

        <div class="about__content">
            <p class="eyebrow" data-reveal>{{ $block['eyebrow'] }}</p>
            <h2 class="h2" id="about-title" data-reveal>{{ site()->format($block['title']) }}</h2>
            <p class="about__subtitle" data-reveal>{{ $profile['name'] }} | {{ $profile['degrees'] }}</p>

            @if ($bio['quote'])
                <blockquote class="about__quote" data-reveal>“{{ $bio['quote'] }}”</blockquote>
            @endif

            <div class="prose" data-reveal>
                @foreach ($bio['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            @if ($bio['expertise'])
                <h3 class="label-title" data-reveal>{{ $block['expertise_heading'] }}</h3>
                <ul class="expertise">
                    @foreach ($bio['expertise'] as $item)
                        <li data-reveal style="--i: {{ $loop->index }}">
                            <span class="icon-bubble"><x-icon :name="$item['icon']" /></span>
                            {{ $item['title'] }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="btn-row about__ctas" data-reveal>
                <a class="btn btn--teal btn--lg" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $block['call_button'] }}</a>
                @unless ($full)
                    <a class="btn btn--outline btn--lg" href="{{ route('about') }}">{{ $block['profile_button'] }} <x-icon name="arrow-right" /></a>
                @endunless
            </div>
        </div>
    </div>
</section>
