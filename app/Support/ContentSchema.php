<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Reads the editable-content schema in config/cms.php and turns it into
 * resolved values (stored overrides + defaults), validation rules and
 * clean data ready to be saved from the admin form.
 */
class ContentSchema
{
    public function __construct(private ImageUploader $images) {}

    /**
     * @return array<string, array{label: string, group: string, route?: string, description?: string, sections: array<string, array<string, mixed>>}>
     */
    public function screens(): array
    {
        return config('cms.screens', []);
    }

    public function has(string $screen): bool
    {
        return array_key_exists($screen, $this->screens());
    }

    /**
     * @return array{label: string, group: string, route?: string, description?: string, sections: array<string, array<string, mixed>>}
     */
    public function screen(string $screen): array
    {
        return $this->screens()[$screen];
    }

    /**
     * Screens grouped for the admin sidebar.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function grouped(): array
    {
        return collect($this->screens())->groupBy('group', preserveKeys: true)->map->all()->all();
    }

    /**
     * Merge stored values over the schema defaults for one screen.
     *
     * @param  array<string, mixed>  $stored
     * @return array<string, array<string, mixed>>
     */
    public function resolve(string $screen, array $stored): array
    {
        return collect($this->screen($screen)['sections'])
            ->map(fn (array $section, string $key) => $this->resolveFields($section['fields'], $stored[$key] ?? []))
            ->all();
    }

    /**
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private function resolveFields(array $fields, array $stored): array
    {
        $values = [];

        foreach ($fields as $name => $field) {
            $value = array_key_exists($name, $stored) ? $stored[$name] : $this->defaultFor($field);

            if ($field['type'] === 'repeater') {
                $value = array_map(fn ($row) => $this->resolveFields($field['fields'], (array) $row), (array) $value);
            }

            $values[$name] = $value;
        }

        return $values;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function defaultFor(array $field): mixed
    {
        if (array_key_exists('default', $field)) {
            return $field['default'];
        }

        return match ($field['type']) {
            'list', 'days', 'repeater' => [],
            'toggle' => false,
            'image', 'picture' => null,
            default => '',
        };
    }

    /**
     * Validation rules for the admin form of one screen.
     *
     * @return array<string, mixed>
     */
    public function rules(string $screen): array
    {
        $rules = [];

        foreach ($this->screen($screen)['sections'] as $sectionKey => $section) {
            $this->fieldRules($section['fields'], $sectionKey, $rules);
        }

        return $rules;
    }

    /**
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $rules
     */
    private function fieldRules(array $fields, string $path, array &$rules): void
    {
        foreach ($fields as $name => $field) {
            $key = "fields.{$path}.{$name}";
            $presence = ($field['required'] ?? false) ? 'required' : 'nullable';

            $rules[$key] = match ($field['type']) {
                'textarea', 'list' => [$presence, 'string', 'max:10000'],
                'email' => [$presence, 'email', 'max:255'],
                'url' => [$presence, 'url:http,https', 'max:2000'],
                'time' => [$presence, 'date_format:H:i'],
                'icon' => [$presence, Rule::in(array_keys(Icons::PATHS))],
                'select' => [$presence, Rule::in(array_keys($field['options']))],
                'days' => ['nullable', 'array'],
                'toggle' => ['nullable', 'boolean'],
                'repeater' => ['nullable', 'array', 'max:'.($field['max'] ?? 60)],
                'image', 'picture' => ['nullable', 'string', 'max:255', 'regex:/^[\w\-\/.]+$/', 'not_regex:/\.\./'],
                default => [$presence, 'string', 'max:'.($field['max'] ?? 255)],
            };

            if ($field['slug'] ?? false) {
                $rules[$key][] = 'alpha_dash';
            }

            if ($field['distinct'] ?? false) {
                $rules[$key][] = 'distinct';
            }

            match ($field['type']) {
                'days' => $rules["{$key}.*"] = ['integer', 'between:1,7'],
                'image', 'picture' => $rules["uploads.{$path}.{$name}"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
                'repeater' => $this->fieldRules($field['fields'], "{$path}.{$name}.*", $rules),
                default => null,
            };
        }
    }

    /**
     * Friendly attribute names for validation messages ("Title" instead of "fields.hero.title").
     *
     * @return array<string, string>
     */
    public function attributes(string $screen): array
    {
        return collect($this->rules($screen))
            ->keys()
            ->mapWithKeys(fn (string $key) => [$key => Str::of($key)->afterLast('.')->replace('*', '')->headline()->lower()->toString()])
            ->all();
    }

    /**
     * Build the value that gets stored for a screen from the submitted form.
     *
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $uploads
     * @param  array<string, mixed>  $removals
     * @return array<string, array<string, mixed>>
     */
    public function normalize(string $screen, array $input, array $uploads, array $removals): array
    {
        return collect($this->screen($screen)['sections'])
            ->map(fn (array $section, string $key) => $this->normalizeFields(
                $section['fields'],
                (array) ($input[$key] ?? []),
                (array) ($uploads[$key] ?? []),
                (array) ($removals[$key] ?? []),
            ))
            ->all();
    }

    /**
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $uploads
     * @param  array<string, mixed>  $removals
     * @return array<string, mixed>
     */
    private function normalizeFields(array $fields, array $input, array $uploads, array $removals): array
    {
        $values = [];

        foreach ($fields as $name => $field) {
            $value = $input[$name] ?? null;

            $values[$name] = match ($field['type']) {
                'repeater' => collect((array) $value)
                    ->map(fn ($row, $index) => $this->normalizeFields(
                        $field['fields'],
                        (array) $row,
                        (array) Arr::get($uploads, "{$name}.{$index}", []),
                        (array) Arr::get($removals, "{$name}.{$index}", []),
                    ))
                    ->values()
                    ->all(),
                'list' => Str::of((string) $value)->explode("\n")->map(fn ($line) => trim($line))->filter()->values()->all(),
                'days' => collect((array) $value)->map(fn ($day) => (int) $day)->unique()->sort()->values()->all(),
                'toggle' => (bool) $value,
                'image', 'picture' => $this->normalizeImage($field, $value, $uploads[$name] ?? null, (bool) ($removals[$name] ?? false)),
                default => trim((string) $value),
            };
        }

        return $values;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function normalizeImage(array $field, mixed $current, mixed $upload, bool $remove): ?string
    {
        if ($upload instanceof UploadedFile) {
            return $field['type'] === 'picture'
                ? $this->images->storePicture($upload)
                : $this->images->storeImage($upload, $field['keep_format'] ?? false, $field['max_width'] ?? 1200);
        }

        return $remove ? null : ($current ?: null);
    }
}
