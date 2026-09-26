@props(['eyebrow' => null, 'lead' => null, 'align' => 'left', 'tone' => 'default'])
<header {{ $attributes->class(['section-head', 'section-head--center' => $align === 'center', 'section-head--light' => $tone === 'light']) }} data-reveal="blur">
    @if ($eyebrow)
        <p class="eyebrow">{{ $eyebrow }}</p>
    @endif
    <h2 class="h2">{{ $title }}</h2>
    @if ($lead)
        <p class="lead">{{ $lead }}</p>
    @endif
    {{ $slot }}
</header>
