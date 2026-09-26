@php($nav = collect(site('layout.header.nav'))->filter(fn ($item) => Route::has($item['route'])))

<div class="topbar">
    <div class="container container--wide topbar__inner">
        <p class="topbar__item" data-open-status>
            <span class="status-dot" aria-hidden="true"></span>
            <span data-open-label>OPD</span>
            <span class="topbar__muted" data-open-detail>{{ $clinic->hoursSummary() }}</span>
        </p>
        <p class="topbar__item topbar__item--wide">
            <x-icon name="map-pin" /> {{ collect([site('clinic.address.locality'), site('clinic.address.region')])->filter()->implode(', ') }}
        </p>
        <div class="topbar__links">
            <a href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $clinic->phone() }}</a>
            <a href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> WhatsApp</a>
        </div>
    </div>
</div>

<header class="site-header" data-header>
    <div class="container container--wide site-header__inner">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $clinic->name() }} – {{ $clinic->brand() }}, home">
            <span class="brand__mark"><img src="{{ $clinic->logoUrl() }}" alt="" width="230" height="198"></span>
            <span class="brand__text">
                <strong>{{ $clinic->name() }}</strong>
                <span>{{ $clinic->brand() }}</span>
            </span>
        </a>

        <nav class="nav" aria-label="Main" data-nav>
            <span class="nav__indicator" aria-hidden="true"></span>
            <ul class="nav__list">
                @foreach ($nav as $item)
                    <li>
                        <a class="nav__link" href="{{ route($item['route']) }}" @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                            <span class="label-full">{{ $item['label'] }}</span>
                            <span class="label-short">{{ $item['short'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="header-actions">
            <a class="btn btn--outline btn--sm header-call" href="{{ $clinic->phoneHref() }}">
                <x-icon name="phone" /> {{ site('layout.header.call_label') }}
            </a>
            <a class="btn btn--gold btn--sm header-book" href="{{ route('contact') }}#appointment" data-magnetic>
                <x-icon name="calendar-check" /> <span>{{ site('layout.header.book_label') }}</span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
                <span class="sr-only" data-menu-label>Open menu</span>
                <span class="menu-toggle__bars" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <span class="scroll-progress" aria-hidden="true" data-scroll-progress></span>
</header>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
    <nav aria-label="Mobile">
        <ul class="mobile-menu__list">
            @foreach ($nav as $item)
                <li style="--i: {{ $loop->index }}">
                    <a class="mobile-menu__link" href="{{ route($item['route']) }}" @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                        <span>{{ $item['label'] }}</span>
                        <x-icon name="arrow-up-right" />
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
    <div class="mobile-menu__footer">
        <a class="btn btn--gold btn--lg btn--block" href="{{ route('contact') }}#appointment"><x-icon name="calendar-check" /> {{ site('layout.header.book_label') }}</a>
        <a class="btn btn--outline btn--lg btn--block" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ site('layout.header.call_label') }}: {{ $clinic->phone() }}</a>
        <p class="mobile-menu__hours"><x-icon name="clock" /> {{ $clinic->hoursSummary() }}</p>
    </div>
</div>
