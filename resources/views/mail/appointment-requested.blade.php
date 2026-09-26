<x-mail::message>
# New appointment request

A patient has requested an appointment through the website. Please call them to confirm a slot.

<x-mail::table>
| | |
|:--|:--|
| **Name** | {{ $appointment->name }} |
| **Phone** | {{ $appointment->phone }} |
| **Preferred date** | {{ $appointment->preferred_date?->format('D, d M Y') ?? 'Any' }} |
| **Preferred time** | {{ $appointment->preferred_slot ? ucfirst($appointment->preferred_slot) : 'Any' }} |
| **Concern** | {{ $appointment->concernLabel() ?? '—' }} |
</x-mail::table>

@if ($appointment->message)
**Message**

{{ $appointment->message }}
@endif

<x-mail::button :url="'tel:'.$appointment->phone">
Call {{ $appointment->phone }}
</x-mail::button>

Received {{ $appointment->created_at->timezone(config('clinic.timezone'))->format('d M Y, g:i A') }}<br>
{{ site('settings.identity.name') }} – {{ site('settings.identity.brand') }}
</x-mail::message>
