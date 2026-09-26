<x-admin.layout title="Appointments" active="appointments">
    @php($statuses = \App\Models\Appointment::statuses())

    <div class="a-toolbar">
        <nav class="a-tabs" aria-label="Filter by status">
            <a @class(['a-tab', 'is-active' => ! $status]) href="{{ route('admin.appointments.index', array_filter(['q' => $search])) }}">All <span>{{ $counts->sum() }}</span></a>
            @foreach ($statuses as $value => $label)
                <a @class(['a-tab', 'is-active' => $status === $value]) href="{{ route('admin.appointments.index', array_filter(['status' => $value, 'q' => $search])) }}">{{ $label }} <span>{{ $counts[$value] ?? 0 }}</span></a>
            @endforeach
        </nav>
        <form class="a-search" method="GET" action="{{ route('admin.appointments.index') }}">
            @if ($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <input class="a-input" type="search" name="q" value="{{ $search }}" placeholder="Search name or phone">
            <button class="a-btn a-btn--soft" type="submit">Search</button>
        </form>
    </div>

    <section class="a-card a-card--flush">
        @forelse ($appointments as $appointment)
            <article class="a-appt">
                <div class="a-appt__main">
                    <div class="a-appt__title">
                        <strong>{{ $appointment->name }}</strong>
                        <span class="a-status a-status--{{ $appointment->status }}">{{ $statuses[$appointment->status] ?? $appointment->status }}</span>
                    </div>
                    <dl class="a-appt__meta">
                        <div><dt>Phone</dt><dd><a href="tel:{{ $appointment->phone }}">{{ $appointment->phone }}</a></dd></div>
                        <div><dt>Preferred</dt><dd>{{ $appointment->preferred_date?->format('D, d M Y') ?? 'Any day' }}{{ $appointment->preferred_slot ? ' · '.ucfirst($appointment->preferred_slot) : '' }}</dd></div>
                        <div><dt>Concern</dt><dd>{{ $appointment->concernLabel() ?? '—' }}</dd></div>
                        <div><dt>Received</dt><dd>{{ $appointment->created_at->timezone(config('clinic.timezone'))->format('d M Y, g:i A') }}</dd></div>
                    </dl>
                    @if ($appointment->message)
                        <p class="a-appt__message">{{ $appointment->message }}</p>
                    @endif
                </div>
                <div class="a-appt__actions">
                    <a class="a-btn a-btn--soft a-btn--sm" href="{{ $appointment->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> WhatsApp</a>
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="a-inline-form">
                        @csrf
                        @method('PATCH')
                        <select class="a-input a-input--sm" name="status" aria-label="Status for {{ $appointment->name }}" data-autosubmit>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($appointment->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <noscript><button class="a-btn a-btn--sm" type="submit">Update</button></noscript>
                    </form>
                    <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" data-confirm="Delete the request from {{ $appointment->name }}?">
                        @csrf
                        @method('DELETE')
                        <button class="a-icon-btn a-icon-btn--danger" type="submit" title="Delete" aria-label="Delete"><x-icon name="x" /></button>
                    </form>
                </div>
            </article>
        @empty
            <p class="a-empty">No appointment requests{{ $status || $search ? ' match this filter' : ' yet' }}.</p>
        @endforelse
    </section>

    {{ $appointments->links('admin.pagination') }}
</x-admin.layout>
