<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
        $form = $this->formFor('layout');
        $links = $form['fields']['footer']['links'];
        $form['fields']['footer']['links'] = [
            'n1' => ['label' => 'Parking & directions', 'url' => '/contact#map'],
            4 => $links[4],
            0 => $links[0],
        ];

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'layout'), $form)
            ->assertSessionHasNoErrors();

        $this->assertSame(
            ['Parking & directions', $links[4]['label'], $links[0]['label']],
            array_column(site('layout.footer.links'), 'label'),
        );
        $this->get(route('home'))
            ->assertSee('Parking &amp; directions', false)
            ->assertDontSee($links[2]['label']);
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

    public function test_uploaded_gallery_photo_appears_on_the_gallery_page_under_its_category(): void
    {
        $form = $this->formFor('gallery');
        $form['fields']['photos']['items'] = [
            'n1' => ['image' => '', 'caption' => 'Free hearing camp', 'category' => 'Health camps'],
            0 => $form['fields']['photos']['items'][0],
        ];

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'gallery'), $form + [
                'uploads' => ['photos' => ['items' => ['n1' => ['image' => UploadedFile::fake()->image('camp.jpg', 800, 600)]]]],
            ])
            ->assertSessionHasNoErrors();

        $name = site('gallery.photos.items.0.image');
        $this->assertMatchesRegularExpression('#^uploads/camp-[a-z0-9]{6}$#', $name);

        $this->get(route('gallery'))
            ->assertSee("images/{$name}-640.webp", false)
            ->assertSee('Free hearing camp')
            ->assertSee('data-filter="health-camps"', false)
            ->assertSee('data-filter="hospital"', false)
            ->assertDontSee('Endoscopy suite');
    }

    public function test_gallery_filter_is_hidden_when_photos_share_one_category(): void
    {
        $form = $this->formFor('gallery');
        foreach ($form['fields']['photos']['items'] as $index => $photo) {
            $form['fields']['photos']['items'][$index]['category'] = '';
        }

        $this->actingAs(User::factory()->create())
            ->put(route('admin.content.update', 'gallery'), $form)
            ->assertSessionHasNoErrors();

        $this->get(route('gallery'))
            ->assertSee('Endoscopy suite')
            ->assertDontSee('data-gallery-filter', false);
    }

    public function test_saved_patient_information_links_are_moved_to_the_gallery(): void
    {
        SiteContent::create(['key' => 'patient', 'value' => ['hero' => ['title' => 'Old page']]]);
        SiteContent::create(['key' => 'layout', 'value' => [
            'header' => ['nav' => [
                ['route' => 'home', 'label' => 'Start', 'short' => 'Start'],
                ['route' => 'patient-info', 'label' => 'Patient Info', 'short' => 'Info'],
            ]],
            'footer' => ['extra_links' => [['label' => 'Patient Information', 'url' => '/patient-information']]],
        ]]);

        (require database_path('migrations/2026_09_30_175933_replace_patient_page_with_gallery.php'))->up();

        $this->assertDatabaseMissing(SiteContent::class, ['key' => 'patient']);
        $this->assertSame(['home', 'gallery'], array_column(site('layout.header.nav'), 'route'));
        $this->get(route('home'))
            ->assertSee('class="nav__link" href="'.route('gallery').'"', false)
            ->assertSee('Photo Gallery')
            ->assertDontSee('Patient Info');
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

    public function test_changed_timings_are_shown_in_the_contact_block(): void
    {
        $this->actingAs(User::factory()->create())->put(route('admin.content.update', 'clinic'), $this->formFor('clinic', [
            'hours' => ['sessions' => [['label' => 'Full day', 'from' => '09:30', 'to' => '18:00']]],
        ]));

        $this->get(route('contact'))->assertSee('9:30 AM – 6:00 PM');
    }

    public function test_saved_booking_content_is_rewritten_when_booking_is_removed(): void
    {
        SiteContent::create(['key' => 'home', 'value' => ['layout' => ['sections' => [['section' => 'hero'], ['section' => 'appointment']]]]]);
        SiteContent::create(['key' => 'clinic', 'value' => ['numbers' => [
            'whatsapp_greeting' => 'Hello Divine ENT Centre, I would like to book an appointment.',
            'notify_email' => 'desk@example.com',
        ]]]);
        SiteContent::create(['key' => 'layout', 'value' => ['footer' => ['links' => [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Book Online', 'url' => '/contact#appointment'],
        ]]]]);

        (require database_path('migrations/2026_09_30_181800_remove_appointment_booking.php'))->up();

        $this->assertFalse(Schema::hasTable('appointments'));
        $this->assertSame(['hero', 'location'], array_column(site('home.layout.sections'), 'section'));
        $this->assertSame('Hello Divine ENT Centre, I have a query.', site('clinic.numbers.whatsapp_greeting'));
        $this->assertArrayNotHasKey('notify_email', SiteContent::where('key', 'clinic')->value('value')['numbers']);
        $this->get(route('home'))
            ->assertSee('id="location-title"', false)
            ->assertDontSee('Book Online')
            ->assertDontSee('#appointment', false);
    }
}
