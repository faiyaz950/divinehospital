<x-admin.layout title="Dashboard" active="dashboard">
    <div class="a-stats">
        <a class="a-stat a-stat--accent" href="{{ route('admin.appointments.index', ['status' => 'new']) }}">
            <span class="a-stat__value">{{ $stats['new'] }}</span>
            <span class="a-stat__label">New requests to call back</span>
        </a>
        <div class="a-stat">
            <span class="a-stat__value">{{ $stats['today'] }}</span>
            <span class="a-stat__label">Requests today</span>
        </div>
        <div class="a-stat">
            <span class="a-stat__value">{{ $stats['week'] }}</span>
            <span class="a-stat__label">Last 7 days</span>
        </div>
        <div class="a-stat">
            <span class="a-stat__value">{{ $stats['total'] }}</span>
            <span class="a-stat__label">All time</span>
        </div>
    </div>

    <div class="a-columns">
        <section class="a-card">
            <header class="a-card__head a-card__head--row">
                <h2>Latest appointment requests</h2>
                <a class="a-link" href="{{ route('admin.appointments.index') }}">View all →</a>
            </header>
            @forelse ($latest as $appointment)
                <div class="a-list-item">
                    <div>
                        <strong>{{ $appointment->name }}</strong>
                        <span class="a-muted a-small">
                            {{ $appointment->concernLabel() ?? 'General' }}
                            · {{ $appointment->preferred_date?->format('d M') ?? 'Any day' }}{{ $appointment->preferred_slot ? ', '.ucfirst($appointment->preferred_slot) : '' }}
                        </span>
                    </div>
                    <div class="a-list-item__end">
                        <span class="a-status a-status--{{ $appointment->status }}">{{ \App\Models\Appointment::statuses()[$appointment->status] ?? $appointment->status }}</span>
                        <a class="a-btn a-btn--soft a-btn--sm" href="tel:{{ $appointment->phone }}"><x-icon name="phone" /> {{ $appointment->phone }}</a>
                    </div>
                </div>
            @empty
                <p class="a-empty">No appointment requests yet.</p>
            @endforelse
        </section>

        <section class="a-card">
            <header class="a-card__head">
                <h2>Edit the website</h2>
                <p class="a-muted">Every page and section can be changed here.</p>
            </header>
            @foreach ($groups as $group => $screens)
                <p class="a-nav__group a-nav__group--dark">{{ $group }}</p>
                <div class="a-tiles">
                    @foreach ($screens as $key => $screen)
                        <a class="a-tile" href="{{ route('admin.content.edit', $key) }}">
                            <x-icon :name="$screen['icon'] ?? 'info'" />
                            <span>
                                <strong>{{ $screen['label'] }}</strong>
                                <small class="a-muted">{{ isset($updated[$key]) ? 'Edited '.\Illuminate\Support\Carbon::parse($updated[$key])->diffForHumans() : 'Original content' }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </section>
    </div>
</x-admin.layout>
