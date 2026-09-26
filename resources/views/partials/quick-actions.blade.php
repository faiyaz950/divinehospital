{{-- Mobile: sticky bottom action bar · Desktop: floating WhatsApp button --}}
<nav class="action-bar" aria-label="Quick actions">
    <a href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /><span>Call</span></a>
    <a class="action-bar__wa" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /><span>WhatsApp</span></a>
    <a class="action-bar__book" href="{{ route('contact') }}#appointment"><x-icon name="calendar-check" /><span>Book Visit</span></a>
</nav>

<a class="wa-fab" data-magnetic href="{{ $clinic->whatsappUrl(site('clinic.numbers.whatsapp_greeting')) }}" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
    <x-icon name="whatsapp" />
</a>
