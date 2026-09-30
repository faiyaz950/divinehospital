@php
    $showHead ??= true;
    $block = site('common.location');
@endphp
<section class="section location" id="location" aria-labelledby="location-title">
    <div class="container">
        @if ($showHead)
            <x-section-head :eyebrow="$block['eyebrow']" :lead="$block['lead']">
                <x-slot:title><span id="location-title">{{ site()->format($block['title']) }}</span></x-slot:title>
            </x-section-head>
        @else
            <h2 class="sr-only" id="location-title">{{ str_replace('*', '', $block['title']) }}</h2>
        @endif

        <div class="location__grid">
            <div class="info-dark" data-reveal="left">
                <div class="info-row">
                    <span class="info-row__icon"><x-icon name="building" /></span>
                    <div>
                        <h3>Hospital</h3>
                        <p>{{ $clinic->name() }} / {{ $clinic->brand() }}</p>
                        <p class="info-row__muted">{{ $clinic->doctorName() }}, {{ site('doctor.profile.short_degree') }}</p>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-row__icon"><x-icon name="map-pin" /></span>
                    <div>
                        <h3>Address</h3>
                        <p>{{ $clinic->address() }}</p>
                        <a class="link-arrow link-arrow--gold" href="{{ $clinic->directionsUrl() }}" target="_blank" rel="noopener">Get directions <x-icon name="arrow-up-right" /></a>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-row__icon"><x-icon name="clock" /></span>
                    <div class="info-row__grow">
                        <h3>Consultation Timings</h3>
                        <table class="hours-table">
                            <caption class="sr-only">Consultation timings</caption>
                            @foreach ($clinic->sessions() as $session)
                                <tr data-hours-row="open">
                                    <th scope="row">{{ $loop->first ? site('clinic.hours.days_label') : '' }}<span class="hours-table__session">{{ $session['label'] }}</span></th>
                                    <td>{{ $session['range'] }}</td>
                                </tr>
                            @endforeach
                            <tr data-hours-row="closed">
                                <th scope="row">{{ site('clinic.hours.closed_label') }}</th>
                                <td>{{ site('clinic.hours.closed_note') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-row__icon"><x-icon name="phone" /></span>
                    <div>
                        <h3>Contact Numbers</h3>
                        <p><span class="info-row__muted">Reception:</span> <a href="{{ $clinic->phoneHref() }}">{{ $clinic->phone() }}</a></p>
                        <p><span class="info-row__muted">WhatsApp Inquiries:</span> <a href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener">{{ $clinic->whatsapp() }}</a></p>
                    </div>
                </div>
                @if ($clinic->email())
                    <div class="info-row">
                        <span class="info-row__icon"><x-icon name="mail" /></span>
                        <div>
                            <h3>Email</h3>
                            <p><a href="mailto:{{ $clinic->email() }}">{{ $clinic->email() }}</a></p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="map-card" data-reveal="right" style="--i:1">
                <iframe src="{{ $clinic->mapEmbedUrl() }}" title="Map showing the location of {{ $clinic->name() }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                <a class="btn btn--light btn--sm map-card__cta" href="{{ $clinic->directionsUrl() }}" target="_blank" rel="noopener"><x-icon name="navigation" /> Open in Google Maps</a>
            </div>
        </div>
    </div>
</section>
