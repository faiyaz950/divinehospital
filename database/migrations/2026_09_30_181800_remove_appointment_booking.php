<?php

use App\Models\SiteContent;
use App\Support\Content;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Online appointment booking was removed from the website: drop the requests table
     * and rewrite saved content that still points visitors at the booking form.
     */
    public function up(): void
    {
        Schema::dropIfExists('appointments');

        $this->updateContent('home', function (array $value): array {
            if (isset($value['layout']['sections'])) {
                $value['layout']['sections'] = array_map(
                    fn (array $block): array => ($block['section'] ?? null) === 'appointment' ? ['section' => 'location'] : $block,
                    $value['layout']['sections'],
                );
            }

            return $value;
        });

        $this->updateContent('common', function (array $value): array {
            unset($value['appointment']);

            return $value;
        });

        $this->updateContent('clinic', function (array $value): array {
            unset($value['numbers']['notify_email']);

            if (($value['numbers']['whatsapp_greeting'] ?? null) === 'Hello Divine ENT Centre, I would like to book an appointment.') {
                $value['numbers']['whatsapp_greeting'] = 'Hello Divine ENT Centre, I have a query.';
            }

            if (($value['hours']['closed_note'] ?? null) === 'By prior appointment / Emergency only') {
                $value['hours']['closed_note'] = 'Emergency only';
            }

            return $value;
        });

        $this->updateContent('layout', function (array $value): array {
            foreach (['links', 'extra_links'] as $list) {
                if (isset($value['footer'][$list])) {
                    $value['footer'][$list] = array_values(array_filter(
                        $value['footer'][$list],
                        fn (array $link): bool => ! str_contains($link['url'] ?? '', '#appointment'),
                    ));
                }
            }

            if (isset($value['header']['nav'])) {
                $value['header']['nav'] = array_map(
                    fn (array $item): array => ($item['label'] ?? null) === 'Contact & Appointments' ? ['label' => 'Contact Us'] + $item : $item,
                    $value['header']['nav'],
                );
            }

            return $value;
        });

        app(Content::class)->flush();
    }

    public function down(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('phone', 20);
            $table->date('preferred_date')->nullable();
            $table->string('preferred_slot', 20)->nullable();
            $table->string('concern', 40)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $change
     */
    private function updateContent(string $key, callable $change): void
    {
        $content = SiteContent::query()->where('key', $key)->first();

        if ($content) {
            $content->update(['value' => $change($content->value)]);
        }
    }
};
