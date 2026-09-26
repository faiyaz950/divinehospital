<x-layout :title="site('home.seo.title')" :description="site('home.seo.description')">
    @foreach (site('home.layout.sections') as $block)
        @includeIf('sections.'.$block['section'])
    @endforeach
</x-layout>
