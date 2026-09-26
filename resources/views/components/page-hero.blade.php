@props(['crumb', 'lead' => null, 'image' => null, 'imageAlt' => ''])
<section @class(['page-hero', 'page-hero--media' => $image])>
    <div class="page-hero__bg" aria-hidden="true"></div>
    <div class="container page-hero__grid">
        <div class="page-hero__content">
            <nav class="breadcrumbs rise" aria-label="Breadcrumb">
                <ol>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li aria-current="page">{{ $crumb }}</li>
                </ol>
            </nav>
            <h1 class="page-hero__title" data-split>{{ $title }}</h1>
            <script>window.splitWords && splitWords(document.currentScript.previousElementSibling)</script>
            @if ($lead)
                <p class="page-hero__lead lead rise" style="--i:4">{{ $lead }}</p>
            @endif
            @if (trim($slot))
                <div class="page-hero__actions rise" style="--i:6">{{ $slot }}</div>
            @endif
        </div>
        @if ($image)
            <figure class="page-hero__media wipe">
                <x-picture :name="$image" :alt="$imageAlt" sizes="(min-width: 1024px) 560px, 100vw" eager />
            </figure>
        @endif
    </div>
</section>
