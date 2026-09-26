<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login · {{ $clinic->name() }}</title>
    <link rel="icon" href="{{ site()->asset('settings.branding.favicon') ?? asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body class="a-login">
    <main class="a-login__card">
        <img class="a-login__logo" src="{{ $clinic->logoUrl() }}" alt="" width="64" height="55">
        <h1>{{ $clinic->name() }}</h1>
        <p class="a-muted">Sign in to manage the website and appointments.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="a-stack">
            @csrf
            <div class="a-field">
                <label class="a-label" for="email">Email</label>
                <input class="a-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" @error('email') aria-invalid="true" @enderror>
                @error('email')
                    <p class="a-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="a-field">
                <label class="a-label" for="password">Password</label>
                <input class="a-input" id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <label class="a-check"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
            <button class="a-btn a-btn--primary a-btn--block" type="submit">Sign in</button>
        </form>

        <a class="a-login__back" href="{{ route('home') }}">← Back to website</a>
    </main>
</body>
</html>
