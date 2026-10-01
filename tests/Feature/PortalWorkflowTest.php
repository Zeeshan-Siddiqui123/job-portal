<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Application;
use App\Models\Category;
use App\Models\JobListing;
use App\Models\Notification;
use App\Models\User;
use Tests\TestCase;

class PortalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function jobData(): array
    {
        return [
            'title' => 'Laravel Developer',
            'category_id' => Category::firstOrCreate(['slug' => 'engineering'], ['name' => 'Engineering'])->id,
            'company' => 'Example Company', 'location' => 'Lahore', 'type' => 'Full-Time',
            'experience_level' => 'Mid Level', 'description' => 'Build and maintain Laravel applications with our development team.',
            'featured' => '1',
        ];
    }

    public function test_admin_can_review_applicants_for_own_job_and_update_status_with_notifications(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $candidate = User::factory()->create(['role' => 'job_seeker']);
        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('No applications received yet.');
        $this->post('/jobs', $this->jobData())->assertSessionHasNoErrors();
        $job = JobListing::firstOrFail();
        $this->assertSame($admin->id, $job->employer_id);
        $this->actingAs($candidate)->post('/jobs/'.$job->id.'/apply', [
            'cover_letter' => 'I have experience building Laravel applications.',
            'resume_url' => 'https://example.test/resume.pdf',
        ])->assertSessionHasNoErrors();
        $application = Application::firstOrFail();
        $this->actingAs($admin)->get('/dashboard')->assertOk()
            ->assertSee('Job Applications')->assertSee($candidate->email)
            ->assertSee($job->title)->assertSee('https://example.test/resume.pdf')
            ->assertSee('New Application Received')->assertSee(route('applications.status', $application->id));
        foreach (['Shortlisted', 'Hired', 'Rejected'] as $status) {
            $this->actingAs($admin)->post('/applications/'.$application->id.'/status', ['status' => $status])->assertSessionHasNoErrors();
            $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => $status]);
            $this->assertDatabaseHas('notifications', [
                'user_id' => $candidate->id,
                'message' => 'Your application for "'.$job->title.'" was updated to: '.$status,
            ]);
            $this->actingAs($candidate)->get('/dashboard')->assertOk()->assertSee($status)->assertSee('Application Status Update');
        }
    }

    public function test_admin_can_view_but_cannot_update_applications_for_other_owners(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $candidate = User::factory()->create(['role' => 'job_seeker']);

        foreach (['employer', 'admin'] as $role) {
            $owner = User::factory()->create(['role' => $role]);
            $job = JobListing::create(array_merge($this->jobData(), [
                'employer_id' => $owner->id, 'title' => 'Job owned by '.$role,
            ]));
            $this->actingAs($candidate)->post('/jobs/'.$job->id.'/apply', [
                'cover_letter' => 'I have experience building Laravel applications.',
            ])->assertSessionHasNoErrors();
            $application = Application::where('job_id', $job->id)->firstOrFail();
            $notificationCount = Notification::count();

            $this->actingAs($admin)->get('/dashboard')->assertOk()
                ->assertSee($job->title)->assertSee($candidate->email)
                ->assertSee('View only')
                ->assertDontSee(route('applications.status', $application->id));
            foreach (['Shortlisted', 'Hired', 'Rejected'] as $status) {
                $this->post('/applications/'.$application->id.'/status', ['status' => $status])->assertForbidden();
                $this->assertSame('Pending', $application->fresh()->status);
                $this->assertDatabaseCount('notifications', $notificationCount);
            }
        }
    }

    public function test_registration_job_posting_application_notifications_and_profile_flow(): void
    {
        $this->post('/register', [
            'name' => 'Hiring Manager', 'email' => 'employer@example.test', 'password' => 'password123',
            'password_confirmation' => 'password123', 'role' => 'employer', 'company_name' => 'Example Company',
        ])->assertRedirect('/dashboard');
        $employer = User::where('email', 'employer@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($employer);
        $this->get('/jobs-create')->assertOk()->assertSee('Category');
        $data = $this->jobData();
        $this->post('/jobs', $data)->assertSessionHasNoErrors();
        $job = JobListing::firstOrFail();
        $this->assertSame($employer->id, $job->employer_id);
        $this->get('/jobs?keyword=Laravel&location=Lahore&type=Full-Time&category='.$job->category_id)->assertOk()->assertSee('Laravel Developer');
        $this->get('/jobs?location=Karachi')->assertOk()->assertDontSee('Laravel Developer');
        $this->get('/jobs/'.$job->id.'/edit')->assertOk();
        $this->put('/jobs/'.$job->id, array_merge($data, ['title' => 'Senior Laravel Developer', 'status' => 'Open']))->assertSessionHasNoErrors();
        $this->post('/logout')->assertRedirect('/jobs');
        $this->post('/register', [
            'name' => 'Candidate', 'email' => 'candidate@example.test', 'password' => 'password123',
            'password_confirmation' => 'password123', 'role' => 'job_seeker',
        ])->assertRedirect('/dashboard');
        $seeker = User::where('email', 'candidate@example.test')->firstOrFail();
        $this->post('/profile', ['name' => 'Updated Candidate', 'skills' => 'PHP, Laravel', 'location' => 'Lahore'])->assertRedirect('/profile');
        $this->get('/profile')->assertOk()->assertSeeText('PHP')->assertSeeText('Laravel');
        $this->get('/jobs/'.$job->id)->assertOk()->assertSee('Apply Now');
        $this->post('/jobs/'.$job->id.'/apply', ['cover_letter' => 'I have the Laravel experience needed for this position.'])->assertRedirect('/dashboard');
        $application = Application::firstOrFail();
        $this->assertDatabaseHas('notifications', ['user_id' => $employer->id, 'title' => 'New Application Received']);
        $this->actingAs($employer)->get('/dashboard')->assertOk()->assertSee('New Application Received')->assertSee('Updated Candidate');
        $this->post('/applications/'.$application->id.'/status', ['status' => 'Shortlisted'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'Shortlisted']);
        $this->actingAs($seeker)->get('/dashboard')->assertOk()->assertSee('Application Status Update')->assertSee('Shortlisted');
        $notice = Notification::where('user_id', $seeker->id)->firstOrFail();
        $this->patch('/notifications/'.$notice->id.'/read')->assertSessionHasNoErrors();
        $this->assertTrue($notice->fresh()->is_read);
        $this->actingAs($employer)->delete('/jobs/'.$job->id)->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('job_listings', ['id' => $job->id]);
    }

    public function test_closed_jobs_and_non_seekers_cannot_receive_applications(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $job = JobListing::create(array_merge($this->jobData(), ['employer_id' => $employer->id, 'status' => 'Closed']));
        $payload = ['cover_letter' => 'A valid cover letter with sufficient detail.'];
        foreach (['employer', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->post('/jobs/'.$job->id.'/apply', $payload)->assertForbidden();
        }
        $seeker = User::factory()->create(['role' => 'job_seeker']);
        $this->actingAs($seeker)->get('/jobs/'.$job->id)->assertOk()->assertSee('Applications closed')->assertDontSee('> Apply Now', false);
        $this->post('/jobs/'.$job->id.'/apply', $payload)->assertSessionHasErrors('application');
        $this->assertDatabaseCount('applications', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_duplicates_invalid_input_and_unauthorized_status_changes_are_rejected(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $seeker = User::factory()->create(['role' => 'job_seeker']);
        $job = JobListing::create(array_merge($this->jobData(), ['employer_id' => $employer->id]));
        $url = '/jobs/'.$job->id.'/apply';
        $this->actingAs($seeker)->post($url, ['cover_letter' => 'short'])->assertSessionHasErrors('cover_letter');
        $payload = ['cover_letter' => 'A valid cover letter with sufficient detail.'];
        $this->post($url, $payload)->assertRedirect('/dashboard');
        $this->post($url, $payload)->assertSessionHasErrors('application');
        $this->assertDatabaseCount('applications', 1);
        $this->assertDatabaseCount('notifications', 1);
        $application = Application::firstOrFail();
        $other = User::factory()->create(['role' => 'employer']);
        $this->actingAs($other)->post('/applications/'.$application->id.'/status', ['status' => 'Hired'])->assertForbidden();
        $this->patch('/notifications/'.Notification::firstOrFail()->id.'/read')->assertNotFound();
        $this->actingAs($employer)->post('/applications/'.$application->id.'/status', ['status' => 'Invalid'])->assertSessionHasErrors('status');
        $this->post('/applications/'.$application->id.'/status', ['status' => 'Pending'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_guests_and_other_employers_cannot_manage_someone_elses_job(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $job = JobListing::create(array_merge($this->jobData(), ['employer_id' => $employer->id]));
        $this->get('/jobs-create')->assertRedirect('/login');
        $other = User::factory()->create(['role' => 'employer']);
        $this->actingAs($other)->put('/jobs/'.$job->id, array_merge($this->jobData(), ['status' => 'Closed']))->assertSessionHas('error');
        $this->delete('/jobs/'.$job->id)->assertSessionHas('error');
        $this->assertSame('Open', $job->fresh()->status);
        $this->actingAs(User::factory()->create(['role' => 'job_seeker']))->post('/jobs', $this->jobData())->assertSessionHas('error');
        $this->assertDatabaseCount('job_listings', 1);
    }

    public function test_login_and_logout_work_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/jobs');
        $this->assertGuest();
    }
}
