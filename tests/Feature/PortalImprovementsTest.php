<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Category;
use App\Models\JobListing;
use App\Models\Notification;
use App\Models\PortalSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_settings_are_available_in_page_titles_and_layouts(): void
    {
        $settings = PortalSetting::current();
        $settings->site_name = 'Punjab Careers';
        $settings->save();

        $owner = User::factory()->create(['role' => 'employer']);
        $job = $this->createJob($owner);

        foreach (['/', '/jobs', '/jobs/'.$job->id, '/login', '/register'] as $url) {
            $this->get($url)->assertOk()->assertSee('Punjab Careers');
        }

        $this->actingAs($owner)->get('/jobs-create')->assertOk()->assertSee('Punjab Careers');

        $candidate = User::factory()->create(['role' => 'job_seeker']);
        foreach (['/dashboard', '/profile', '/profile/edit'] as $url) {
            $this->actingAs($candidate)->get($url)->assertOk()->assertSee('Punjab Careers');
        }
    }

    public function test_job_listing_uses_default_portal_settings_when_none_are_saved(): void
    {
        $this->get('/jobs')->assertOk()->assertSee('Browse Job Listings | JobPortal');
    }

    private function createJob(User $owner, array $attributes = []): JobListing
    {
        return JobListing::create(array_merge([
            'employer_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['slug' => 'engineering'], ['name' => 'Engineering'])->id,
            'title' => 'Laravel Developer', 'company' => 'Example Company',
            'location' => 'Lahore', 'type' => 'Full-Time', 'experience_level' => 'Mid Level',
            'description' => 'Build and maintain Laravel applications.', 'status' => 'Open',
        ], $attributes));
    }

    private function submitApplication(JobListing $job, User $candidate): Application
    {
        return Application::create([
            'job_id' => $job->id, 'job_seeker_id' => $candidate->id,
            'cover_letter' => 'My private cover letter with Laravel experience.',
            'resume_url' => 'https://example.test/resume.pdf', 'status' => 'Pending',
        ]);
    }

    public function test_admin_job_management_is_restricted_and_lists_older_jobs_with_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employer = User::factory()->create(['role' => 'employer']);
        $oldJob = $this->createJob($employer, ['title' => 'Older matching role', 'status' => 'Closed', 'type' => 'Remote']);
        for ($i = 0; $i < 15; $i++) {
            $this->createJob($employer, ['title' => 'Recent vacancy '.$i]);
        }
        $this->get('/admin/jobs')->assertRedirect('/login');
        foreach (['employer', 'job_seeker'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get('/admin/jobs')->assertForbidden();
        }
        $this->actingAs($admin)->get('/admin/jobs')->assertOk()->assertSee('Manage jobs')
            ->assertViewHas('jobs', fn ($jobs) => $jobs->total() === 16 && $jobs->count() === 15);
        $this->get('/admin/jobs?page=2')->assertOk()->assertSee('Older matching role')
            ->assertSee(route('jobs.edit', $oldJob->id))->assertSee(route('jobs.destroy', $oldJob->id));
        $this->get('/admin/jobs?search=Older&status=Closed&type=Remote&category='.$oldJob->category_id)
            ->assertOk()->assertSee('Older matching role')->assertDontSee('Recent vacancy')
            ->assertViewHas('jobs', fn ($jobs) => $jobs->total() === 1);
        $this->get('/admin/jobs?search=missing')->assertOk()->assertSee('No jobs match your filters.');
        $this->get('/admin/jobs?search=Example')->assertOk()
            ->assertViewHas('jobs', fn ($jobs) => str_contains($jobs->nextPageUrl(), 'search=Example'));
        $this->get('/admin/jobs?status=Invalid&type=Invalid&category=99999')
            ->assertSessionHasErrors(['status', 'type', 'category']);
    }

    public function test_profile_access_is_limited_to_self_admin_and_the_candidates_employer(): void
    {
        $candidate = User::factory()->create(['role' => 'job_seeker', 'phone' => '03001234567']);
        $owner = User::factory()->create(['role' => 'employer']);
        $otherEmployer = User::factory()->create(['role' => 'employer']);
        $otherCandidate = User::factory()->create(['role' => 'job_seeker']);
        $admin = User::factory()->create(['role' => 'admin']);
        $url = '/candidate/'.$candidate->id;
        $this->get($url)->assertRedirect('/login');
        $this->actingAs($owner)->get($url)->assertForbidden();
        $this->submitApplication($this->createJob($owner), $candidate);
        foreach ([$candidate, $owner, $admin] as $viewer) {
            $this->actingAs($viewer)->get($url)->assertOk()->assertSee('03001234567');
        }
        foreach ([$otherEmployer, $otherCandidate] as $viewer) {
            $this->actingAs($viewer)->get($url)->assertForbidden()->assertDontSee('03001234567');
            $this->get('/profile')->assertOk()->assertSee($viewer->email);
        }
        $this->actingAs($owner)->get('/candidate/'.$admin->id)->assertForbidden();
        $this->get('/candidate/99999')->assertNotFound();
    }

    public function test_application_details_are_visible_only_on_authorized_dashboards(): void
    {
        $owner = User::factory()->create(['role' => 'employer']);
        $candidate = User::factory()->create(['role' => 'job_seeker']);
        $application = $this->submitApplication($this->createJob($owner), $candidate);
        $admin = User::factory()->create(['role' => 'admin']);
        foreach ([$owner, $admin] as $viewer) {
            $this->actingAs($viewer)->get('/dashboard')->assertOk()
                ->assertSee('View Application')->assertSee($application->cover_letter)
                ->assertSee($application->resume_url)->assertSee('Applied on')
                ->assertSee(route('profile.candidate', $candidate->id));
        }
        $this->actingAs(User::factory()->create(['role' => 'employer']))->get('/dashboard')
            ->assertOk()->assertDontSee($application->cover_letter)->assertDontSee($application->resume_url);
        $application->update(['cover_letter' => '<script>alert(1)</script>', 'resume_url' => null]);
        $this->actingAs($owner)->get('/dashboard')->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)->assertSee('No resume provided.');
    }

    public function test_notification_history_is_paginated_and_private_and_can_be_marked_read(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $oldest = Notification::create(['user_id' => $user->id, 'title' => 'Oldest notice', 'message' => 'Old activity']);
        for ($i = 0; $i < 15; $i++) {
            Notification::create(['user_id' => $user->id, 'title' => 'New notice '.$i, 'message' => 'New activity']);
        }
        $foreign = Notification::create(['user_id' => $other->id, 'title' => 'Private foreign notice', 'message' => 'Private']);
        $this->get('/notifications')->assertRedirect('/login');
        $this->actingAs($user)->get('/notifications')->assertOk()->assertDontSee('Private foreign notice')
            ->assertViewHas('notifications', fn ($items) => $items->total() === 16 && $items->count() === 15);
        $this->get('/notifications?page=2')->assertOk()->assertSee('Oldest notice');
        $this->from('/notifications?page=2')->patch('/notifications/'.$oldest->id.'/read')->assertRedirect('/notifications?page=2');
        $this->assertTrue($oldest->fresh()->is_read);
        $this->patch('/notifications/'.$foreign->id.'/read')->assertNotFound();
        $this->assertFalse($foreign->fresh()->is_read);
        $this->actingAs(User::factory()->create())->get('/notifications')->assertOk()->assertSee('No notifications yet.');
    }

    public function test_related_jobs_exclude_closed_jobs_and_other_categories(): void
    {
        $owner = User::factory()->create(['role' => 'employer']);
        $job = $this->createJob($owner);
        $related = $this->createJob($owner, ['title' => 'Open related job']);
        $this->createJob($owner, ['title' => 'Closed related job', 'status' => 'Closed']);
        $category = Category::create(['name' => 'Other category', 'slug' => 'other']);
        $this->createJob($owner, ['title' => 'Different category job', 'category_id' => $category->id]);
        $this->get('/jobs/'.$job->id)->assertOk()->assertSee('Open related job')
            ->assertDontSee('Closed related job')->assertDontSee('Different category job')
            ->assertViewHas('relatedJobs', fn ($jobs) => $jobs->pluck('id')->all() === [$related->id]);
    }

    public function test_applied_indicator_is_specific_to_the_logged_in_candidate(): void
    {
        $owner = User::factory()->create(['role' => 'employer']);
        $candidate = User::factory()->create(['role' => 'job_seeker']);
        $job = $this->createJob($owner);
        $this->get('/jobs')->assertOk()->assertSee('View & Apply', false)->assertDontSee('applied-label');
        $this->submitApplication($job, $candidate);
        $this->actingAs($candidate)->get('/jobs')->assertOk()->assertSee('applied-label')
            ->assertDontSee('View & Apply', false)->assertSee(route('jobs.show', $job->id));
        $this->actingAs(User::factory()->create(['role' => 'job_seeker']))->get('/jobs')->assertOk()
            ->assertSee('View & Apply', false)->assertDontSee('applied-label');
        $this->createJob($owner, ['title' => 'Not applied yet']);
        $this->actingAs($candidate)->get('/jobs')->assertOk()->assertSee('applied-label')->assertSee('View & Apply', false);
    }
}
