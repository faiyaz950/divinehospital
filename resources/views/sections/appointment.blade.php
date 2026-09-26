@php
    $booked = session('appointment');
    $selectedConcern = old('concern', request()->query('concern'));
    $selectedSlot = old('preferred_slot');
    $showHead ??= true;
    $block = site('common.appointment');
@endphp
<section class="section appointment" id="appointment" aria-labelledby="appointment-title">
    <div class="container">
        @if ($showHead)
            <x-section-head :eyebrow="$block['eyebrow']" :lead="$block['lead']">
                <x-slot:title><span id="appointment-title">{{ site()->format($block['title']) }}</span></x-slot:title>
            </x-section-head>
        @else
            <h2 class="sr-only" id="appointment-title">{{ str_replace('*', '', $block['title']) }}</h2>
        @endif

        <div class="appointment__grid">
            <div class="form-card" data-reveal="left">
                @if ($booked)
                    <div class="form-success" role="status" tabindex="-1" data-autofocus>
                        <span class="form-success__icon"><x-icon name="check" /></span>
                        <h3>Thank you, {{ $booked['name'] }}!</h3>
                        <p>{{ site()->format($block['success'], ['phone' => '<strong>'.e($booked['phone']).'</strong>']) }}</p>
                        <div class="btn-row btn-row--center">
                            <a class="btn btn--whatsapp btn--lg" href="{{ $clinic->whatsappUrl($booked['whatsapp_message']) }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> {{ $block['whatsapp_button'] }}</a>
                        </div>
                        <p class="form-note">Need help sooner? Call <a href="{{ $clinic->phoneHref() }}">{{ $clinic->phone() }}</a></p>
                    </div>
                @else
                    <div class="form-card__head">
                        <span class="icon-bubble icon-bubble--gold"><x-icon name="calendar-check" /></span>
                        <div>
                            <h3>{{ $block['form_title'] }}</h3>
                            <p>{{ $block['form_text'] }} Fields marked <span class="req">*</span> are required.</p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="form-alert" role="alert" tabindex="-1" data-autofocus>
                            <x-icon name="alert" />
                            <span>Please correct the highlighted {{ Str::plural('field', $errors->count()) }} and try again.</span>
                        </div>
                    @endif

                    <form class="form" method="POST" action="{{ route('appointments.store') }}" data-appointment-form>
                        @csrf
                        <div class="hp" aria-hidden="true">
                            <label for="f-website">Leave this field empty</label>
                            <input id="f-website" type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-grid">
                            <x-field name="name" label="Patient name" required autocomplete="name" maxlength="80" placeholder="Full name" />
                            <x-field name="phone" type="tel" label="Mobile number" required autocomplete="tel" inputmode="tel" maxlength="16" placeholder="10-digit mobile number" />
                            <x-field name="preferred_date" type="date" label="Preferred date" :min="$clinic->today()->toDateString()" />

                            <fieldset class="field">
                                <legend>Preferred time</legend>
                                <div class="choice-group">
                                    <label class="choice">
                                        <input type="radio" name="preferred_slot" value="morning" @checked($selectedSlot === 'morning')>
                                        <span><x-icon name="sun" /> Morning</span>
                                    </label>
                                    <label class="choice">
                                        <input type="radio" name="preferred_slot" value="evening" @checked($selectedSlot === 'evening')>
                                        <span><x-icon name="sunset" /> Evening</span>
                                    </label>
                                </div>
                            </fieldset>

                            <div class="field field--full">
                                <label for="f-concern">Area of concern</label>
                                <select class="input" id="f-concern" name="concern" @error('concern') aria-invalid="true" @enderror>
                                    <option value="">Select (optional)</option>
                                    @foreach ($clinic->concerns() as $value => $label)
                                        <option value="{{ $value }}" @selected($selectedConcern === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <x-field name="message" type="textarea" label="Symptoms or message" full maxlength="1000" rows="3" placeholder="Briefly describe the problem (optional)" />
                        </div>

                        <button class="btn btn--gold btn--lg btn--block form__submit" type="submit" data-submit>
                            <span class="btn__spinner" aria-hidden="true"></span>
                            <span data-submit-label>{{ $block['submit'] }}</span>
                            <x-icon name="arrow-right" />
                        </button>
                        <p class="form-note"><x-icon name="shield-check" /> {{ $block['privacy'] }}</p>
                    </form>
                @endif
            </div>

            <div class="contact-panel" id="location">
                <div class="info-dark" data-reveal="right" style="--i:1">
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
                            <p><span class="info-row__muted">Appointments / Reception:</span> <a href="{{ $clinic->phoneHref() }}">{{ $clinic->phone() }}</a></p>
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

                <div class="map-card" data-reveal="right" style="--i:3">
                    <iframe src="{{ $clinic->mapEmbedUrl() }}" title="Map showing the location of {{ $clinic->name() }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    <a class="btn btn--light btn--sm map-card__cta" href="{{ $clinic->directionsUrl() }}" target="_blank" rel="noopener"><x-icon name="navigation" /> Open in Google Maps</a>
                </div>
            </div>
        </div>
    </div>
</section>
