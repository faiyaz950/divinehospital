{{-- Mobile: sticky bottom action bar · Desktop: floating WhatsApp button --}}
<nav class="action-bar" aria-label="Quick actions">
    <a href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /><span>Call</span></a>
    <a class="action-bar__wa" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /><span>WhatsApp</span></a>
    <a class="action-bar__map" href="{{ $clinic->directionsUrl() }}" target="_blank" rel="noopener"><x-icon name="navigation" /><span>Directions</span></a>
</nav>

<a class="wa-fab" data-magnetic href="{{ $clinic->whatsappUrl(site('clinic.numbers.whatsapp_greeting')) }}" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
    <x-icon name="whatsapp" />
</a>
