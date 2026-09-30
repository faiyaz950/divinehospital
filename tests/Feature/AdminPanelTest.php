<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_dashboard_links_to_every_content_screen(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.content.edit', 'gallery'), false)
            ->assertDontSee('Appointments');
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
