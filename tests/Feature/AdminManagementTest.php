<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PortalSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_access_or_change_users_and_settings(): void
    {
        $target = User::factory()->create();
        $this->get('/admin/users')->assertRedirect('/login');
        foreach (['employer', 'job_seeker'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $this->get('/admin/users')->assertForbidden();
            $this->get('/admin/settings')->assertForbidden();
            $this->post('/admin/users', [])->assertForbidden();
            $this->put('/admin/users/'.$target->id, [])->assertForbidden();
            $this->delete('/admin/users/'.$target->id)->assertForbidden();
            $this->put('/admin/settings', [])->assertForbidden();
        }
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_create_search_edit_and_delete_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/users/create')->assertOk();
        $data = ['name' => 'Managed User', 'email' => 'managed@example.test', 'role' => 'job_seeker', 'password' => 'password123', 'password_confirmation' => 'password123'];
        $this->post('/admin/users', $data)->assertRedirect('/admin/users');
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->get('/admin/users?search=Managed&role=job_seeker')->assertOk()->assertSee('managed@example.test');
        $this->get('/admin/users/'.$user->id.'/edit')->assertOk();
        $this->put('/admin/users/'.$user->id, ['name' => 'Updated User', 'email' => 'updated@example.test', 'role' => 'employer', 'password' => ''])->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated User', 'role' => 'employer']);
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
        $this->delete('/admin/users/'.$user->id)->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_or_demote_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->delete('/admin/users/'.$admin->id)->assertSessionHasErrors('user');
        $this->put('/admin/users/'.$admin->id, ['name' => $admin->name, 'email' => $admin->email, 'role' => 'employer'])->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_settings_persist_and_gate_new_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/settings')->assertOk();
        $settings = ['site_name' => 'Punjab Careers', 'support_email' => 'support@example.test', 'registration_open' => '0', 'job_posting_open' => '0', 'applications_open' => '0'];
        $this->put('/admin/settings', $settings)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('portal_settings', ['id' => 1, 'site_name' => 'Punjab Careers', 'registration_open' => 0]);
        $this->get('/jobs')->assertOk()->assertSee('Punjab Careers')->assertSee('support@example.test');
        $this->post('/jobs', [])->assertForbidden();
        $this->get('/jobs-create')->assertRedirect('/dashboard');
        $this->actingAs(User::factory()->create(['role' => 'job_seeker']))->post('/jobs/1/apply', [])->assertForbidden();
        $this->post('/logout');
        $this->get('/register')->assertRedirect('/login');
        $this->post('/register', [])->assertForbidden();
        $this->actingAs($admin)->put('/admin/settings', array_merge($settings, ['registration_open' => '1', 'job_posting_open' => '1', 'applications_open' => '1']))->assertSessionHasNoErrors();
        $this->assertTrue(PortalSetting::current()->registration_open);
        $this->get('/register')->assertOk();
        $this->get('/jobs-create')->assertOk();
        $this->assertDatabaseCount('portal_settings', 1);
    }

    public function test_user_and_settings_validation_reject_bad_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post('/admin/users', ['name' => 'Test', 'email' => $admin->email, 'role' => 'unknown', 'password' => 'short'])->assertSessionHasErrors(['email', 'role', 'password']);
        $this->put('/admin/settings', ['site_name' => '', 'support_email' => 'invalid', 'registration_open' => 'yes'])->assertSessionHasErrors(['site_name', 'support_email', 'registration_open']);
        $this->assertDatabaseCount('portal_settings', 0);
    }
}
