@props(['title', 'active' => null])
@php
    $groups = app(\App\Support\ContentSchema::class)->grouped();
    $newCount = \App\Models\Appointment::where('status', 'new')->count();
    $asset = fn (string $path) => asset($path).'?v='.filemtime(public_path($path));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · Admin · {{ $clinic->name() }}</title>
    <link rel="icon" href="{{ site()->asset('settings.branding.favicon') ?? asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ $asset('css/admin.css') }}">
</head>
<body class="a-body">
    <input type="checkbox" id="a-nav-toggle" class="a-nav-toggle" hidden>
    <aside class="a-sidebar">
        <a class="a-brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ $clinic->logoUrl() }}" alt="" width="36" height="31">
            <span><strong>{{ $clinic->name() }}</strong><small>Admin panel</small></span>
        </a>

        <nav class="a-nav" aria-label="Admin">
            <a @class(['a-nav__link', 'is-active' => $active === 'dashboard']) href="{{ route('admin.dashboard') }}"><x-icon name="sparkles" /> Dashboard</a>
            <a @class(['a-nav__link', 'is-active' => $active === 'appointments']) href="{{ route('admin.appointments.index') }}">
                <x-icon name="calendar-check" /> Appointments
                @if ($newCount)
                    <span class="a-badge">{{ $newCount }}</span>
                @endif
            </a>

            @foreach ($groups as $group => $screens)
                <p class="a-nav__group">{{ $group }}</p>
                @foreach ($screens as $key => $screen)
                    <a @class(['a-nav__link', 'is-active' => $active === "content.{$key}"]) href="{{ route('admin.content.edit', $key) }}"><x-icon :name="$screen['icon'] ?? 'info'" /> {{ $screen['label'] }}</a>
                @endforeach
            @endforeach

            <p class="a-nav__group">Account</p>
            <a @class(['a-nav__link', 'is-active' => $active === 'account']) href="{{ route('admin.account.edit') }}"><x-icon name="users" /> My account</a>
        </nav>
    </aside>
    <label for="a-nav-toggle" class="a-scrim" aria-hidden="true"></label>

    <div class="a-main">
        <header class="a-topbar">
            <label for="a-nav-toggle" class="a-menu-btn" aria-label="Open menu"><span></span></label>
            <h1 class="a-topbar__title">{{ $title }}</h1>
            <div class="a-topbar__actions">
                <a class="a-btn a-btn--ghost" href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="arrow-up-right" /> <span>View website</span></a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="a-btn a-btn--ghost" type="submit"><x-icon name="x" /> <span>Log out</span></button>
                </form>
            </div>
        </header>

        <main class="a-content">
            @if (session('status'))
                <div class="a-flash a-flash--ok" role="status"><x-icon name="check-circle" /> {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="a-flash a-flash--error" role="alert">
                    <x-icon name="alert" />
                    <span>Please fix the {{ Str::plural('field', $errors->count()) }} marked in red below.</span>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script src="{{ $asset('js/admin.js') }}" defer></script>
</body>
</html>
