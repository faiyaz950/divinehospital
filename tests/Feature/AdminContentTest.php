<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('images/uploads'));

        parent::tearDown();
    }

    /**
     * The form posts every field of a screen; start from what the editor would show.
     *
     * @return array<string, mixed>
     */
    private function formFor(string $screen, array $overrides = []): array
    {
        $fields = collect(config("cms.screens.{$screen}.sections"))
            ->map(fn (array $section, string $key) => $this->formValues($section['fields'], site("{$screen}.{$key}")))
            ->all();

        foreach ($overrides as $section => $values) {
            $fields[$section] = array_replace($fields[$section], $values);
        }

        return ['fields' => $fields];
    }

    /**
     * @param  array<string, array<string, mixed>>  $schema
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function formValues(array $schema, array $values): array
    {
        return collect($schema)->map(fn (array $field, string $name) => match ($field['type']) {
            'list' => implode("\n", $values[$name]),
            'repeater' => array_map(fn (array $row) => $this->formValues($field['fields'], $row), $values[$name]),
            default => $values[$name],
        })->all();
    }

    public function test_every_content_screen_opens_in_the_editor(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (config('cms.screens') as $screen => $definition) {
            $this->get(route('admin.content.edit', $screen))
                ->assertOk()
                ->assertSee($definition['label']);
        }
    }

    public function test_every_content_screen_saves_unchanged_without_errors(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (array_keys(config('cms.screens')) as $screen) {
            $this->put(route('admin.content.update', $screen), $this->formFor($screen))
                ->assertSessionHasNoErrors();
        }

        $this->get(route('home'))->assertOk()->assertSee('Advanced ENT &amp; Head-Neck', false);
    }

    public function test_unknown_screen_is_not_found(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.content.edit', 'nope'))
            ->assertNotFound();
    }

    public function test_guests_cannot_edit_content(): void
    {
        $this->put(route('admin.content.update', 'home'), $this->formFor('home'))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_saved_text_appears_on_the_website_with_accent_formatting(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'home'), $this->formFor('home', [
                'hero' => ['title' => 'Best *ENT care* in town'],
            ]))
            ->assertRedirect(route('admin.content.edit', 'home'))
            ->assertSessionHas('status');

        $this->get(route('home'))->assertSee('Best <em>ENT care</em> in town', false);
    }

    public function test_admin_text_is_escaped_on_the_website(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'home'), $this->formFor('home', [
                'hero' => ['title' => '<script>alert(1)</script> *Hi*'],
            ]));

        $this->get(route('home'))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; <em>Hi</em>', false);
    }

    public function test_repeater_rows_can_be_reordered_removed_and_added(): void
    {
        $form = $this->formFor('patient');
        $faqs = $form['fields']['faq']['items'];
        $form['fields']['faq']['items'] = [
            'n1' => ['q' => 'Is parking available?', 'a' => 'Yes, in front of the hospital.'],
            5 => $faqs[5],
            0 => $faqs[0],
        ];

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'patient'), $form)
            ->assertSessionHasNoErrors();

        $this->assertSame(
            ['Is parking available?', $faqs[5]['q'], $faqs[0]['q']],
            array_column(site('patient.faq.items'), 'q'),
        );
        $this->get(route('patient-info'))
            ->assertSee('Is parking available?')
            ->assertDontSee($faqs[1]['q']);
    }

    public function test_one_per_line_lists_are_saved_as_items(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'common'), $this->formFor('common', [
                'marquee' => ['items' => "Ear cleaning\n\n  Hearing aids  \n"],
            ]));

        $this->assertSame(['Ear cleaning', 'Hearing aids'], site('common.marquee.items'));
    }

    public function test_invalid_content_is_rejected(): void
    {
        $form = $this->formFor('specialities');
        $form['fields']['list']['items'][1]['key'] = 'ear';
        $form['fields']['list']['items'][2]['title'] = '';

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'specialities'), $form)
            ->assertSessionHasErrors(['fields.list.items.1.key', 'fields.list.items.2.title']);

        $this->assertDatabaseCount('site_contents', 0);
    }

    public function test_uploaded_photo_is_converted_to_responsive_webp_files(): void
    {
        $form = $this->formFor('home');

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'home'), $form + [
                'uploads' => ['hero' => ['image' => UploadedFile::fake()->image('New Front.jpg', 1200, 800)]],
            ])
            ->assertSessionHasNoErrors();

        $name = site('home.hero.image');
        $this->assertMatchesRegularExpression('#^uploads/new-front-[a-z0-9]{6}$#', $name);
        $this->assertFileExists(public_path("images/{$name}-640.webp"));
        $this->assertFileExists(public_path("images/{$name}-1024.webp"));
        $this->assertFileDoesNotExist(public_path("images/{$name}-1600.webp"));

        $this->get(route('home'))->assertSee("images/{$name}-1024.webp", false);
    }

    public function test_optional_image_can_be_removed(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'doctor'), $this->formFor('doctor') + [
                'remove' => ['profile' => ['photo' => '1']],
            ]);

        $this->assertNull(site('doctor.profile.photo'));
        $this->get(route('about'))->assertDontSee('images/doctor.webp', false)->assertSee('profile-card__initials', false);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'home'), $this->formFor('home') + [
                'uploads' => ['hero' => ['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]],
            ])
            ->assertSessionHasErrors('uploads.hero.image');
    }

    public function test_screen_can_be_restored_to_the_original_content(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('admin.content.update', 'home'), $this->formFor('home', ['hero' => ['title' => 'Temporary']]));

        $this->actingAs($user)->delete(route('admin.content.reset', 'home'))->assertRedirect(route('admin.content.edit', 'home'));

        $this->assertDatabaseMissing(SiteContent::class, ['key' => 'home']);
        $this->assertSame(config('cms.screens.home.sections.hero.fields.title.default'), site('home.hero.title'));
    }

    public function test_home_page_blocks_follow_the_saved_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'home'), $this->formFor('home', [
                'layout' => ['sections' => [['section' => 'why'], ['section' => 'hero']]],
            ]));

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'id="hero-title"'), strpos($html, 'id="why-title"'));
        $this->assertStringNotContainsString('id="testimonials-title"', $html);
    }

    public function test_changed_timings_and_concerns_are_used_by_the_appointment_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('admin.content.update', 'clinic'), $this->formFor('clinic', [
            'hours' => ['sessions' => [['label' => 'Full day', 'from' => '09:30', 'to' => '18:00']]],
        ]));
        $this->actingAs($user)->put(route('admin.content.update', 'common'), $this->formFor('common', [
            'appointment' => ['concerns' => [['value' => 'allergy', 'label' => 'Allergy testing']]],
        ]));

        $this->get(route('contact'))
            ->assertSee('9:30 AM – 6:00 PM')
            ->assertSee('<option value="allergy"', false)
            ->assertDontSee('<option value="ear"', false);

        $this->post(route('appointments.store'), ['name' => 'Asha', 'phone' => '9876543210', 'concern' => 'ear'])
            ->assertSessionHasErrors('concern');
    }
}
