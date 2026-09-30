<x-admin.layout title="Dashboard" active="dashboard">
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
</x-admin.layout>
