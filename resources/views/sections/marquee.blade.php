<div class="marquee" role="region" aria-label="Key treatments">
    <div class="marquee__track">
        @foreach ([false, true] as $duplicate)
            <ul class="marquee__list" @if ($duplicate) aria-hidden="true" @endif>
                @foreach (site('common.marquee.items') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @endforeach
    </div>
</div>
