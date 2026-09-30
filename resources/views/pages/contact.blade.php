@php($hero = site('contact.hero'))
<x-layout :title="site('contact.seo.title')" :description="site('contact.seo.description')">
    <x-page-hero :crumb="$hero['crumb']" :lead="$hero['lead']">
        <x-slot:title>{{ site()->format($hero['title']) }}</x-slot:title>
        <div class="quick-contacts">
            <a class="quick-contact spotlight" href="{{ $clinic->phoneHref() }}">
                <span class="icon-bubble"><x-icon name="phone" /></span>
                <span><small>{{ $hero['call_label'] }}</small>{{ $clinic->phone() }}</span>
            </a>
            <a class="quick-contact spotlight" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener">
                <span class="icon-bubble icon-bubble--wa"><x-icon name="whatsapp" /></span>
                <span><small>{{ $hero['whatsapp_label'] }}</small>{{ $clinic->whatsapp() }}</span>
            </a>
            @if ($clinic->email())
                <a class="quick-contact spotlight" href="mailto:{{ $clinic->email() }}">
                    <span class="icon-bubble"><x-icon name="mail" /></span>
                    <span><small>{{ $hero['email_label'] }}</small>{{ $clinic->email() }}</span>
                </a>
            @endif
            <a class="quick-contact spotlight" href="{{ $clinic->directionsUrl() }}" target="_blank" rel="noopener">
                <span class="icon-bubble"><x-icon name="navigation" /></span>
                <span><small>{{ $hero['directions_label'] }}</small>{{ site('clinic.address.locality') ?: site('clinic.address.city') }}</span>
            </a>
        </div>
    </x-page-hero>

    @include('sections.location', ['showHead' => false])
</x-layout>
