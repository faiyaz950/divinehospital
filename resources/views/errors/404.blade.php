<x-layout title="Page not found">
    <section class="page-hero page-hero--error">
        <div class="page-hero__bg" aria-hidden="true"></div>
        <div class="container error-page">
            <p class="error-page__code">404</p>
            <h1 class="page-hero__title">We couldn’t find <em>that page</em></h1>
            <p class="lead">The page may have moved. Head back home or call us directly.</p>
            <div class="btn-row btn-row--center">
                <a class="btn btn--teal btn--lg" href="{{ route('home') }}">Go to homepage</a>
                <a class="btn btn--gold btn--lg" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> Call {{ $clinic->phone() }}</a>
            </div>
        </div>
    </section>
</x-layout>
