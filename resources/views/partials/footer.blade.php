<footer class="site-footer">
    <div class="container">
        <div class="footer-cta" data-reveal="scale">
            <div>
                <p class="eyebrow eyebrow--light">{{ $clinic->brand() }}</p>
                <h2 class="footer-cta__title">{{ site()->rich('layout.footer.cta_title') }}</h2>
                <p class="footer-cta__text">{{ site('layout.footer.cta_text') }}</p>
            </div>
            <div class="btn-row">
                <a class="btn btn--gold btn--lg" href="{{ $clinic->whatsappUrl(site('clinic.numbers.whatsapp_greeting')) }}" target="_blank" rel="noopener" data-magnetic><x-icon name="whatsapp" /> {{ site('layout.footer.cta_button') }}</a>
                <a class="btn btn--outline-light btn--lg" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $clinic->phone() }}</a>
            </div>
        </div>

        <div class="footer-grid">
            <div class="footer-brand">
                <a class="brand brand--light" href="{{ route('home') }}">
                    <span class="brand__mark"><img src="{{ $clinic->logoUrl() }}" alt="" width="230" height="198" loading="lazy"></span>
                    <span class="brand__text">
                        <strong>{{ $clinic->name() }}</strong>
                        <span>{{ $clinic->brand() }}</span>
                    </span>
                </a>
                @if (site('settings.identity.name_hi'))
                    <p class="footer-brand__hi" lang="hi">{{ site('settings.identity.name_hi') }}</p>
                @endif
                <p>{{ site('layout.footer.about') }}</p>
            </div>

            <nav aria-label="Quick links">
                <h2 class="footer-title">{{ site('layout.footer.links_title') }}</h2>
                <ul class="footer-links">
                    @foreach (site('layout.footer.links') as $link)
                        <li><a href="{{ $clinic->link($link['url']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div>
                <h2 class="footer-title">{{ site('layout.footer.specialities_title') }}</h2>
                <ul class="footer-links">
                    @foreach (site('specialities.list.items') as $speciality)
                        <li><a href="{{ route('services') }}#{{ $speciality['key'] }}">{{ $speciality['title'] }}</a></li>
                    @endforeach
                    @foreach (site('layout.footer.extra_links') as $link)
                        <li><a href="{{ $clinic->link($link['url']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="footer-title">{{ site('layout.footer.contact_title') }}</h2>
                <ul class="footer-contact">
                    <li><x-icon name="map-pin" /><span>{{ $clinic->address() }}</span></li>
                    <li><x-icon name="phone" /><a href="{{ $clinic->phoneHref() }}">{{ $clinic->phone() }}</a></li>
                    <li><x-icon name="whatsapp" /><a href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp {{ $clinic->whatsapp() }}</a></li>
                    @if ($clinic->email())
                        <li><x-icon name="mail" /><a href="mailto:{{ $clinic->email() }}">{{ $clinic->email() }}</a></li>
                    @endif
                    <li><x-icon name="clock" /><span>{{ $clinic->hoursSummary() }}<br>{{ site('clinic.hours.closed_label') }}: {{ site('clinic.hours.closed_note') }}</span></li>
                </ul>
            </div>
        </div>

        @if (site('layout.footer.disclaimer'))
            <div class="footer-disclaimer">
                <x-icon name="info" />
                <p>{{ site()->rich('layout.footer.disclaimer') }}</p>
            </div>
        @endif

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ site('layout.footer.copyright') }}</p>
            <p>{{ $clinic->doctorName() }}, {{ site('doctor.profile.short_degree') }}</p>
        </div>
    </div>
</footer>
