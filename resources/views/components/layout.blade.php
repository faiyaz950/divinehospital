@props([
    'title' => null,
    'description' => null,
])
@php
    $siteName = $clinic->name().' – '.$clinic->brand();
    $pageTitle = $title ? "{$title} | {$siteName}" : "{$siteName} | ".site('settings.seo.home_title');
    $description = $description ?: site('settings.seo.description');
    $asset = fn (string $path) => asset($path).'?v='.filemtime(public_path($path));
    $ogImage = site()->asset('settings.branding.og_image');
    $favicon = site()->asset('settings.branding.favicon') ?? asset('images/favicon.png');
@endphp
<!DOCTYPE html>
<html lang="en-IN" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#073b3f">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_IN">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap">
    <link rel="stylesheet" href="{{ $asset('css/app.css') }}">

    <script>
        document.documentElement.classList.replace('no-js', 'js');
        // Safety net: if app.js never runs, show all scroll-reveal content anyway.
        setTimeout(() => window.__divineReady || document.documentElement.classList.add('reveal-all'), 3000);

        // A skipped page transition (e.g. resizing mid-navigation) is harmless; don't report it as an error.
        ['pageswap', 'pagereveal'].forEach((type) => addEventListener(type, (event) => {
            const vt = event.viewTransition;
            if (vt) [vt.ready, vt.finished, vt.updateCallbackDone].forEach((p) => p?.catch(() => {}));
        }));

        // Splits a headline into words for the entrance animation (runs inline, right after the heading is parsed).
        window.splitWords = (el) => {
            if (!el || el.classList.contains('is-split')) return;
            if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
                const label = el.textContent.replace(/\s+/g, ' ').trim();
                const visual = document.createElement('span');
                let index = 0;
                visual.setAttribute('aria-hidden', 'true');
                visual.append(...el.childNodes);
                const walk = (node) => [...node.childNodes].forEach((child) => {
                    if (child.nodeType === 1) return walk(child);
                    if (child.nodeType !== 3) return;
                    const frag = document.createDocumentFragment();
                    child.textContent.split(/(\s+)/).forEach((part) => {
                        if (!part) return;
                        if (!part.trim()) return frag.append(' ');
                        const word = document.createElement('span');
                        word.className = 'word';
                        word.style.setProperty('--w', index++);
                        word.textContent = part;
                        frag.append(word);
                    });
                    child.replaceWith(frag);
                });
                walk(visual);
                const sr = document.createElement('span');
                sr.className = 'sr-only';
                sr.textContent = label;
                el.append(sr, visual);
                el.style.setProperty('--words', index);
            }
            el.classList.add('is-split');
        };
    </script>
    <script type="application/ld+json">{!! $clinic->jsonLd() !!}</script>
</head>
<body>
    <a class="skip-link" href="#main">Skip to main content</a>

    @include('partials.header')

    <main id="main" tabindex="-1" class="page-main">
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.quick-actions')

    <button class="to-top" type="button" aria-label="Back to top" data-to-top>
        <svg class="to-top__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" pathLength="100" /></svg>
        <x-icon name="arrow-right" class="to-top__icon" />
    </button>

    <script type="application/json" id="clinic-hours">@json($clinic->hoursForScript())</script>
    <script src="{{ $asset('js/app.js') }}" defer></script>
</body>
</html>
