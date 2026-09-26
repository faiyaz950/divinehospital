<?php

use App\Support\Content;

if (! function_exists('site')) {
    /**
     * Admin-editable website content, e.g. site('home.hero.title').
     * Call without arguments to get the Content service (site()->rich(...)).
     */
    function site(?string $key = null, mixed $default = null): mixed
    {
        $content = app(Content::class);

        return $key === null ? $content : $content->get($key, $default);
    }
}
