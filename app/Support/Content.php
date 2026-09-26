<?php

namespace App\Support;

use App\Models\SiteContent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/**
 * Website copy edited in the admin panel. Values are read with dot keys
 * ("home.hero.title" = screen.section.field) and fall back to the defaults
 * in config/cms.php. Available in views through the site() helper.
 */
class Content
{
    private const CACHE_KEY = 'site-content';

    /** @var array<string, mixed>|null */
    private ?array $stored = null;

    /** @var array<string, array<string, mixed>> */
    private array $resolved = [];

    public function __construct(private ContentSchema $schema) {}

    public function get(string $key, mixed $default = null): mixed
    {
        [$screen, $path] = array_pad(explode('.', $key, 2), 2, null);

        if (! $this->schema->has($screen)) {
            return $default;
        }

        $values = $this->resolved[$screen] ??= $this->schema->resolve($screen, $this->stored()[$screen] ?? []);

        return $path === null ? $values : data_get($values, $path, $default);
    }

    /**
     * Escaped text with light formatting: *word* → italic accent, **word** → bold.
     * Extra {tokens} can be swapped for trusted HTML, e.g. ['phone' => '<a …>…</a>'].
     *
     * @param  array<string, string>  $tokens
     */
    public function format(?string $text, array $tokens = []): HtmlString
    {
        $html = e((string) $text);
        $html = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/u', '<em>$1</em>', $html);

        foreach ($tokens as $token => $replacement) {
            $html = str_replace('{'.$token.'}', $replacement, $html);
        }

        return new HtmlString($html);
    }

    /** Shortcut for format(get($key)). */
    public function rich(string $key, array $tokens = []): HtmlString
    {
        return $this->format($this->get($key), $tokens);
    }

    /** Public URL for an uploaded or bundled single image, or null if the file is missing. */
    public function asset(string $key): ?string
    {
        $path = $this->get($key);

        return $path && is_file(public_path($path)) ? asset($path) : null;
    }

    /**
     * Stored overrides for a screen exactly as saved (no defaults).
     *
     * @return array<string, mixed>
     */
    public function stored(): array
    {
        return $this->stored ??= $this->loadStored();
    }

    public function save(string $screen, array $values): void
    {
        SiteContent::updateOrCreate(['key' => $screen], ['value' => $values]);
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->stored = null;
        $this->resolved = [];
    }

    /**
     * @return array<string, mixed>
     */
    private function loadStored(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => SiteContent::query()->pluck('value', 'key')->all());
        } catch (QueryException) {
            // Table not migrated yet — the site still renders with the default copy.
            return [];
        }
    }
}
