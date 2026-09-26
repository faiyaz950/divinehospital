@php($hero = site('home.hero'))
<section class="hero" aria-labelledby="hero-title" data-parallax>
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="container hero__grid">
        <div class="hero__content">
            @if ($hero['pill'])
                <p class="pill rise">
                    <span class="pill__mark"><img src="{{ $clinic->logoUrl() }}" alt="" width="230" height="198"></span>
                    {{ $hero['pill'] }}
                </p>
            @endif

            <h1 class="hero__title" id="hero-title" data-split>
                {{ site()->format($hero['title']) }}
            </h1>
            <script>window.splitWords && splitWords(document.getElementById('hero-title'))</script>

            @if ($hero['lead'])
                <p class="hero__lead lead rise" style="--i:5">
                    {{ site()->format($hero['lead']) }}
                </p>
            @endif

            @if ($hero['highlights'])
                <ul class="hero__badges">
                    @foreach ($hero['highlights'] as $highlight)
                        <li class="rise" style="--i: {{ 6 + $loop->index }}"><span class="tick"><x-icon name="check" /></span>{{ $highlight }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="btn-row hero__ctas rise" style="--i:9">
                <a class="btn btn--gold btn--lg btn--shine" href="#appointment" data-magnetic><x-icon name="calendar-check" /> {{ $hero['primary_button'] }}</a>
                @if ($hero['secondary_button'])
                    <a class="btn btn--outline btn--lg" href="#location" data-magnetic><x-icon name="map-pin" /> {{ $hero['secondary_button'] }}</a>
                @endif
            </div>

            @if ($award = $clinic->awardImageUrl())
                <div class="hero__trust rise" style="--i:10">
                    <span class="medal" style="--medal: url('{{ $award }}')"><img src="{{ $award }}" alt="" width="200" height="200"></span>
                    <div>
                        <strong>{{ $clinic->awardTitle() }}</strong>
                        <span>{{ site('settings.award.hero_text') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="hero__visual">
            <div class="hero__frame wipe">
                <x-picture :name="$hero['image']" :alt="$hero['image_alt']" sizes="(min-width: 1024px) 560px, 100vw" eager />
            </div>

            <div class="float-card float-card--doctor" style="--pop: 0">
                @if ($avatar = $clinic->doctorAvatarUrl())
                    <img class="avatar avatar--photo" src="{{ $avatar }}" alt="" width="160" height="160">
                @else
                    <span class="avatar" aria-hidden="true">{{ site('doctor.profile.initials') }}</span>
                @endif
                <div>
                    <strong>{{ $clinic->doctorName() }}</strong>
                    <span>{{ site('doctor.profile.degrees') }}</span>
                </div>
            </div>

            <div class="float-card float-card--status" data-open-status style="--pop: 1">
                <span class="status-dot" aria-hidden="true"></span>
                <div>
                    <strong data-open-label>OPD {{ site('clinic.hours.days_short') }}</strong>
                    <span data-open-detail>{{ collect($clinic->sessions())->pluck('range')->implode(' · ') }}</span>
                </div>
            </div>

            @if ($hero['tech_title'])
                <div class="float-card float-card--tech" style="--pop: 2">
                    <span class="icon-bubble"><x-icon name="scan" /></span>
                    <div>
                        <strong>{{ $hero['tech_title'] }}</strong>
                        <span>{{ $hero['tech_text'] }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
