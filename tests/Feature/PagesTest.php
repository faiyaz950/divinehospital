<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PagesTest extends TestCase
{
    public static function pages(): array
    {
        return [
            'home' => ['home', 'Advanced ENT &amp; Head-Neck'],
            'about' => ['about', 'Meet Your'],
            'services' => ['services', 'Endoscopic Sinus Surgery (FESS)'],
            'facilities' => ['facilities', 'High-Definition Endoscopy Unit'],
            'gallery' => ['gallery', 'Take a look'],
            'contact' => ['contact', 'Request an appointment'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_renders(string $route, string $expected): void
    {
        $this->get(route($route))
            ->assertOk()
            ->assertSee($expected, false)
            ->assertSee('Medical Disclaimer')
            ->assertSee('application/ld+json', false);
    }

    public function test_navigation_marks_the_current_page(): void
    {
        $html = $this->get(route('facilities'))->getContent();

        $this->assertMatchesRegularExpression('#class="nav__link" href="'.preg_quote(route('facilities'), '#').'"\s+aria-current="page"#', $html);
        $this->assertDoesNotMatchRegularExpression('#class="nav__link" href="'.preg_quote(route('home'), '#').'"\s+aria-current#', $html);
    }

    public function test_contact_details_and_doctor_photo_are_shown(): void
    {
        $this->get(route('home'))
            ->assertSee('href="tel:+919648506121"', false)
            ->assertSee('https://wa.me/919648506121', false)
            ->assertSee('mailto:divinehospital25@gmail.com', false)
            ->assertSee('1/65 Bhusamandi, Kanpur Road, Fatehgarh, Farrukhabad, Uttar Pradesh 209601')
            ->assertSee('images/doctor.webp', false)
            ->assertSee('images/doctor-avatar.webp', false);
    }

    public function test_award_badge_appears_only_in_the_home_hero(): void
    {
        $award = e("Justdial Users' Choice 2026");

        $home = $this->get(route('home'))->getContent();
        $this->assertSame(1, substr_count($home, $award), 'Award should appear exactly once on the home page.');
        $this->assertMatchesRegularExpression('#class="hero__trust.*?'.preg_quote($award, '#').'#s', $home);

        foreach (['about', 'services', 'facilities', 'gallery', 'contact'] as $route) {
            $this->get(route($route))->assertDontSee("Justdial Users' Choice 2026")->assertDontSee('jd-users-choice-2026');
        }
    }

    public function test_sitemap_lists_all_pages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');

        foreach (['home', 'about', 'services', 'facilities', 'gallery', 'contact'] as $route) {
            $response->assertSee(route($route), false);
        }
    }

    public function test_old_patient_information_link_redirects_to_the_gallery(): void
    {
        $this->get('/patient-information')->assertMovedPermanently()->assertRedirect('/gallery');
    }

    public function test_unknown_page_shows_branded_404(): void
    {
        $this->get('/does-not-exist')->assertNotFound()->assertSee('that page');
    }

    public function test_concern_query_preselects_the_form_option(): void
    {
        $this->get(route('contact', ['concern' => 'nose']))
            ->assertSee('<option value="nose" selected', false);
    }
}
