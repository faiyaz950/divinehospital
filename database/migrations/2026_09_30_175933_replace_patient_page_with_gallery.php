<?php

use App\Models\SiteContent;
use App\Support\Content;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The Patient Information page was replaced by the Gallery page: point saved menu
     * items and footer links at the gallery and drop the old page's saved content.
     */
    public function up(): void
    {
        SiteContent::query()->where('key', 'patient')->delete();

        $layout = SiteContent::query()->where('key', 'layout')->first();

        if ($layout) {
            $value = $layout->value;

            if (isset($value['header']['nav'])) {
                $value['header']['nav'] = array_map(
                    fn (array $item): array => ($item['route'] ?? null) === 'patient-info'
                        ? ['route' => 'gallery', 'label' => 'Gallery', 'short' => 'Gallery']
                        : $item,
                    $value['header']['nav'],
                );
            }

            foreach (['links', 'extra_links'] as $list) {
                if (isset($value['footer'][$list])) {
                    $value['footer'][$list] = array_map(
                        fn (array $link): array => ($link['url'] ?? null) === '/patient-information'
                            ? ['label' => 'Photo Gallery', 'url' => '/gallery']
                            : $link,
                        $value['footer'][$list],
                    );
                }
            }

            $layout->update(['value' => $value]);
        }

        app(Content::class)->flush();
    }

    public function down(): void
    {
        //
    }
};
