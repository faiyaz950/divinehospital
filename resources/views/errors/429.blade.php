<x-layout title="Please wait a moment">
    <section class="page-hero page-hero--error">
        <div class="page-hero__bg" aria-hidden="true"></div>
        <div class="container error-page">
            <p class="error-page__code">Hold on</p>
            <h1 class="page-hero__title">Too many requests <em>in a short time</em></h1>
            <p class="lead">Please wait a minute and try again — or simply call or WhatsApp us.</p>
            <div class="btn-row btn-row--center">
                <a class="btn btn--gold btn--lg" href="{{ $clinic->phoneHref() }}"><x-icon name="phone" /> {{ $clinic->phone() }}</a>
                <a class="btn btn--whatsapp btn--lg" href="{{ $clinic->whatsappUrl() }}" target="_blank" rel="noopener"><x-icon name="whatsapp" /> WhatsApp</a>
            </div>
        </div>
    </section>
</x-layout>
