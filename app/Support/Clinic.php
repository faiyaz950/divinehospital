<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * View-friendly accessors for the clinic details edited in the admin panel
 * (see the "clinic", "doctor" and "settings" screens in config/cms.php).
 * Shared with every Blade view as $clinic.
 */
class Clinic
{
    public function __construct(private Content $content) {}

    public function name(): string
    {
        return (string) $this->content->get('settings.identity.name');
    }

    public function brand(): string
    {
        return (string) $this->content->get('settings.identity.brand');
    }

    public function doctorName(): string
    {
        return (string) $this->content->get('doctor.profile.name');
    }

    public function phone(): string
    {
        return (string) $this->content->get('clinic.numbers.phone');
    }

    public function whatsapp(): string
    {
        return (string) $this->content->get('clinic.numbers.whatsapp');
    }

    public function email(): ?string
    {
        return $this->content->get('clinic.numbers.email') ?: null;
    }

    public function phoneHref(): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', $this->phone());
    }

    public function whatsappUrl(?string $message = null): string
    {
        $number = preg_replace('/\D/', '', $this->whatsapp());

        return "https://wa.me/{$number}".($message ? '?text='.rawurlencode($message) : '');
    }

    public function address(): string
    {
        $address = $this->content->get('clinic.address');

        return collect([
            $address['street'],
            $address['locality'],
            trim($address['region'].' '.$address['postal_code']),
        ])->filter()->implode(', ');
    }

    public function mapEmbedUrl(): string
    {
        return $this->content->get('clinic.map.embed_url')
            ?: 'https://www.google.com/maps?q='.urlencode($this->mapQuery()).'&output=embed';
    }

    public function directionsUrl(): string
    {
        return 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($this->mapQuery());
    }

    public function doctorPhotoUrl(): ?string
    {
        return $this->content->asset('doctor.profile.photo');
    }

    public function doctorAvatarUrl(): ?string
    {
        return $this->content->asset('doctor.profile.avatar');
    }

    public function logoUrl(bool $white = false): string
    {
        return $this->content->asset($white ? 'settings.branding.logo_white' : 'settings.branding.logo')
            ?? asset($white ? 'images/logo-mark-white.png' : 'images/logo-mark.png');
    }

    /** Award medal URL, or null when the award is switched off in the admin panel. */
    public function awardImageUrl(): ?string
    {
        return $this->content->get('settings.award.show') ? $this->content->asset('settings.award.image') : null;
    }

    public function awardTitle(): ?string
    {
        return $this->content->get('settings.award.show') ? ($this->content->get('settings.award.title') ?: null) : null;
    }

    /**
     * @return array<int, array{label: string, from: string, to: string, range: string}>
     */
    public function sessions(): array
    {
        return array_map(fn (array $session) => $session + [
            'range' => $this->formatTime($session['from']).' – '.$this->formatTime($session['to']),
        ], $this->rawSessions());
    }

    /** e.g. "Mon–Sat · 10 AM–2 PM & 5–8 PM" */
    public function hoursSummary(): string
    {
        $ranges = array_map(
            fn (array $s) => $this->formatTime($s['from'], compact: true).'–'.$this->formatTime($s['to'], compact: true),
            $this->rawSessions(),
        );

        return $this->content->get('clinic.hours.days_short').' · '.implode(' & ', $ranges);
    }

    /** Opening hours payload consumed by the "Open now" indicator in public/js/app.js. */
    public function hoursForScript(): array
    {
        return [
            'tz' => config('clinic.timezone'),
            'days' => $this->content->get('clinic.hours.open_days'),
            'sessions' => array_map(fn (array $s) => ['from' => $s['from'], 'to' => $s['to']], $this->rawSessions()),
        ];
    }

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('clinic.timezone'));
    }

    /** Link that works for both site paths ("/contact#location") and full URLs. */
    public function link(string $url): string
    {
        return preg_match('#^(https?:|mailto:|tel:)#', $url) ? $url : url($url);
    }

    public function jsonLd(): string
    {
        $dayNames = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $address = $this->content->get('clinic.address');
        $clinicId = url('/').'#clinic';

        $clinic = array_filter([
            '@type' => 'MedicalClinic',
            '@id' => $clinicId,
            'name' => $this->name().' – '.$this->brand(),
            'alternateName' => $this->content->get('settings.identity.name_hi') ?: null,
            'url' => url('/'),
            'logo' => $this->logoUrl(),
            'image' => $this->content->asset('settings.branding.og_image'),
            'telephone' => $this->isPlaceholder($this->phone()) ? null : $this->phone(),
            'email' => $this->email(),
            'medicalSpecialty' => 'Otolaryngologic',
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $address['street'],
                'addressLocality' => $address['city'],
                'addressRegion' => $address['region'],
                'postalCode' => $address['postal_code'],
                'addressCountry' => 'IN',
            ]),
            'openingHoursSpecification' => array_map(fn (array $s) => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array_map(fn (int $d) => $dayNames[$d], $this->content->get('clinic.hours.open_days')),
                'opens' => $s['from'],
                'closes' => $s['to'],
            ], $this->rawSessions()),
        ]);

        $physician = array_filter([
            '@type' => 'Physician',
            'name' => $this->doctorName(),
            'image' => $this->doctorPhotoUrl(),
            'description' => $this->content->get('doctor.profile.degrees'),
            'medicalSpecialty' => 'Otolaryngologic',
            'worksFor' => ['@id' => $clinicId],
        ]);

        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => [$clinic, $physician]],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
        );
    }

    /**
     * @return array<int, array{label: string, from: string, to: string}>
     */
    private function rawSessions(): array
    {
        return collect($this->content->get('clinic.hours.sessions'))
            ->filter(fn (array $s) => preg_match('/^\d{2}:\d{2}$/', $s['from']) && preg_match('/^\d{2}:\d{2}$/', $s['to']))
            ->values()
            ->all();
    }

    private function mapQuery(): string
    {
        return (string) $this->content->get('clinic.map.query');
    }

    private function isPlaceholder(string $value): bool
    {
        return $value === '' || str_contains(strtoupper($value), 'X');
    }

    private function formatTime(string $time, bool $compact = false): string
    {
        $t = CarbonImmutable::createFromFormat('H:i', $time);

        return $compact && $t->minute === 0 ? $t->format('g A') : $t->format('g:i A');
    }
}
