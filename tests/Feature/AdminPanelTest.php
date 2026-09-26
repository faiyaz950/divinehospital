<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function appointment(array $attributes = []): Appointment
    {
        return Appointment::create($attributes + ['name' => 'Ramesh Kumar', 'phone' => '9876543210', 'status' => 'new']);
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.login'))->assertOk()->assertSee('Sign in');
    }

    public function test_admin_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret-pass']);

        $this->post(route('admin.login.store'), ['email' => 'admin@example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('admin.login.store'), ['email' => 'admin@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_dashboard_shows_new_requests(): void
    {
        $this->appointment(['name' => 'Sita Devi']);
        $this->appointment(['name' => 'Old Patient', 'status' => 'completed']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sita Devi')
            ->assertSee('New requests to call back');
    }

    public function test_appointments_can_be_filtered_and_searched(): void
    {
        $this->appointment(['name' => 'Sita Devi']);
        $this->appointment(['name' => 'Mohan Lal', 'phone' => '9123456780', 'status' => 'confirmed']);
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.appointments.index', ['status' => 'confirmed']))
            ->assertSee('Mohan Lal')->assertDontSee('Sita Devi');

        $this->actingAs($admin)->get(route('admin.appointments.index', ['q' => '91234']))
            ->assertSee('Mohan Lal')->assertDontSee('Sita Devi');
    }

    public function test_appointment_status_can_be_updated(): void
    {
        $appointment = $this->appointment();

        $this->actingAs(User::factory()->create())
            ->patch(route('admin.appointments.update', $appointment), ['status' => 'confirmed'])
            ->assertSessionHas('status');
        $this->assertSame('confirmed', $appointment->fresh()->status);

        $this->actingAs(User::factory()->create())
            ->patch(route('admin.appointments.update', $appointment), ['status' => 'bogus'])
            ->assertSessionHasErrors('status');
    }

    public function test_appointment_can_be_deleted(): void
    {
        $appointment = $this->appointment();

        $this->actingAs(User::factory()->create())->delete(route('admin.appointments.destroy', $appointment));

        $this->assertModelMissing($appointment);
    }

    public function test_guests_cannot_manage_appointments(): void
    {
        $appointment = $this->appointment();

        $this->patch(route('admin.appointments.update', $appointment), ['status' => 'confirmed'])->assertRedirect(route('admin.login'));
        $this->delete(route('admin.appointments.destroy', $appointment))->assertRedirect(route('admin.login'));

        $this->assertSame('new', $appointment->fresh()->status);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $form = ['name' => $user->name, 'email' => $user->email, 'password' => 'new-password', 'password_confirmation' => 'new-password'];

        $this->actingAs($user)->put(route('admin.account.update'), $form + ['current_password' => 'wrong'])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('admin.account.update'), $form + ['current_password' => 'old-password'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_admin_can_be_created_from_the_command_line(): void
    {
        $this->artisan('admin:create', ['--name' => 'Reception', '--email' => 'desk@example.com', '--password' => 'strong-pass'])
            ->assertSuccessful();

        $this->assertTrue(Hash::check('strong-pass', User::where('email', 'desk@example.com')->value('password')));

        $this->artisan('admin:create', ['--name' => 'X', '--email' => 'desk@example.com', '--password' => 'short'])
            ->assertFailed();
    }
}
