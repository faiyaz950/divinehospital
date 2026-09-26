<?php

namespace Tests\Feature;

use App\Mail\AppointmentRequested;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ramesh Kumar',
            'phone' => '98765 43210',
            'preferred_date' => now('Asia/Kolkata')->addDay()->toDateString(),
            'preferred_slot' => 'morning',
            'concern' => 'ear',
            'message' => 'Ear pain for two weeks',
        ], $overrides);
    }

    public function test_patient_can_request_an_appointment(): void
    {
        Mail::fake();
        site()->save('clinic', ['numbers' => ['notify_email' => '']]);

        $this->from(route('contact'))
            ->post(route('appointments.store'), $this->validData())
            ->assertRedirect(route('contact').'#appointment')
            ->assertSessionHas('appointment.name', 'Ramesh');

        $this->assertDatabaseHas('appointments', [
            'name' => 'Ramesh Kumar',
            'phone' => '9876543210',
            'preferred_slot' => 'morning',
            'concern' => 'ear',
            'status' => 'new',
        ]);
        Mail::assertNothingSent();
    }

    public function test_success_message_is_shown_after_booking(): void
    {
        $this->from(route('contact'))->followingRedirects()
            ->post(route('appointments.store'), $this->validData())
            ->assertSee('Thank you, Ramesh!')
            ->assertSee('Also confirm on WhatsApp');
    }

    public function test_clinic_is_emailed_when_notify_address_is_set(): void
    {
        Mail::fake();
        site()->save('clinic', ['numbers' => ['notify_email' => 'reception@example.com']]);

        $this->post(route('appointments.store'), $this->validData());

        Mail::assertSent(AppointmentRequested::class, fn ($mail) => $mail->hasTo('reception@example.com'));
    }

    public function test_only_name_and_phone_are_required(): void
    {
        $this->post(route('appointments.store'), ['name' => 'Sita', 'phone' => '+91 6123456789'])
            ->assertSessionHasNoErrors();

        $this->assertSame('+916123456789', Appointment::first()->phone);
    }

    public function test_invalid_input_is_rejected_and_returns_to_the_form(): void
    {
        $this->from(route('home'))
            ->post(route('appointments.store'), $this->validData([
                'name' => '',
                'phone' => '12345',
                'preferred_date' => now('Asia/Kolkata')->subDay()->toDateString(),
                'concern' => 'heart',
            ]))
            ->assertRedirect(route('home').'#appointment')
            ->assertSessionHasErrors(['name', 'phone', 'preferred_date', 'concern']);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post(route('appointments.store'), $this->validData(['website' => 'http://spam.test']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('appointments', 0);
    }
}
