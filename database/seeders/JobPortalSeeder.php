<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\JobListing;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JobPortalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@jobboard.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'headline' => 'Global Portal Administrator',
            'phone' => '+92 300 0000000',
            'location' => 'Lahore, Pakistan',
            'bio' => 'Superadmin overseeing job board metrics, platform integrity, and user roles.',
        ]);

        // 2. Create Employer Users
        $emp1 = User::create([
            'name' => 'TechCorp Global',
            'email' => 'techcorp@example.com',
            'password' => Hash::make('password123'),
            'role' => 'employer',
            'headline' => 'Enterprise Software Solutions',
            'company_name' => 'TechCorp Solutions',
            'company_website' => 'https://techcorp.example.com',
            'phone' => '+92 321 1112233',
            'location' => 'Lahore, Pakistan',
            'bio' => 'Leading IT enterprise delivering cloud solutions and custom software engineering.',
        ]);

        $emp2 = User::create([
            'name' => 'Innovate PK Tech',
            'email' => 'innovate@example.com',
            'password' => Hash::make('password123'),
            'role' => 'employer',
            'headline' => 'Next-Gen Mobile & Web Studio',
            'company_name' => 'Innovate PK',
            'company_website' => 'https://innovate.pk',
            'phone' => '+92 333 4445566',
            'location' => 'Islamabad, Pakistan',
            'bio' => 'Fast-growing software house empowering digital transformation.',
        ]);

        // 3. Create Job Seeker Users
        $seeker1 = User::create([
            'name' => 'Zeeshan Ahmed Siddiq',
            'email' => 'seeker@example.com',
            'password' => Hash::make('password123'),
            'role' => 'job_seeker',
            'headline' => 'Full-Stack PHP Laravel Developer',
            'phone' => '+92 300 1234567',
            'location' => 'Lahore, Pakistan',
            'bio' => 'Passionate web developer with expertise in Laravel, MySQL, REST APIs, and modern responsive front-end design.',
            'skills' => 'PHP 8, Laravel, MySQL, JavaScript, HTML5, CSS3, Git, REST API',
            'resume_link' => 'https://github.com/zeeshan',
        ]);

        $seeker2 = User::create([
            'name' => 'Amina Khan',
            'email' => 'candidate2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'job_seeker',
            'headline' => 'Senior UI/UX & Product Designer',
            'phone' => '+92 301 9876543',
            'location' => 'Karachi, Pakistan',
            'bio' => 'Crafting intuitive user interfaces and slick digital product wireframes for web & mobile apps.',
            'skills' => 'Figma, UI/UX, Glassmorphic Design, HTML/CSS, Tailwind',
            'resume_link' => 'https://linkedin.com/in/aminakhan',
        ]);

        // 4. Create Categories
        $cat1 = Category::create(['name' => 'Software Engineering', 'slug' => 'software-engineering', 'icon' => 'fas fa-code']);
        $cat2 = Category::create(['name' => 'Frontend & UI/UX', 'slug' => 'frontend-ui-ux', 'icon' => 'fas fa-paint-brush']);
        $cat3 = Category::create(['name' => 'Database & Cloud DevOps', 'slug' => 'database-cloud', 'icon' => 'fas fa-server']);
        $cat4 = Category::create(['name' => 'Product & Project Management', 'slug' => 'product-management', 'icon' => 'fas fa-tasks']);

        // 5. Create Job Listings
        $job1 = JobListing::create([
            'employer_id' => $emp1->id,
            'category_id' => $cat1->id,
            'title' => 'Senior Laravel Backend Engineer',
            'company' => 'TechCorp Solutions',
            'location' => 'Lahore, Pakistan',
            'type' => 'Full-Time',
            'salary_range' => 'PKR 180,000 - 250,000 / month',
            'experience_level' => 'Senior Level',
            'description' => 'We are seeking an experienced Senior Laravel Engineer to architect resilient database schemas, REST APIs, and microservices.',
            'requirements' => "- 3+ years experience with PHP 8+ and Laravel framework.\n- Strong expertise in Eloquent ORM, MySQL index tuning, and caching.\n- Solid understanding of OOP design patterns and automated testing.",
            'status' => 'Open',
            'featured' => true,
        ]);

        $job2 = JobListing::create([
            'employer_id' => $emp1->id,
            'category_id' => $cat2->id,
            'title' => 'Full-Stack Developer (Laravel + JS)',
            'company' => 'TechCorp Solutions',
            'location' => 'Remote',
            'type' => 'Remote',
            'salary_range' => 'PKR 150,000 - 200,000 / month',
            'experience_level' => 'Mid Level',
            'description' => 'Join our remote team to build high-grade SaaS applications using Laravel and modern front-end technologies.',
            'requirements' => "- Proficiency in Laravel, Blade, Vanilla JS, and CSS Grid.\n- Hands-on experience building CRUD workflows and API integrations.",
            'status' => 'Open',
            'featured' => true,
        ]);

        $job3 = JobListing::create([
            'employer_id' => $emp2->id,
            'category_id' => $cat2->id,
            'title' => 'Lead UI/UX Designer',
            'company' => 'Innovate PK',
            'location' => 'Islamabad, Pakistan',
            'type' => 'Full-Time',
            'salary_range' => 'PKR 160,000 - 220,000 / month',
            'experience_level' => 'Senior Level',
            'description' => 'Looking for a creative Product Designer to design seamless user journeys, high-fidelity prototypes, and design tokens.',
            'requirements' => "- Mastery of Figma, Adobe XD, and UI prototyping.\n- Portfolio demonstrating responsive web design.",
            'status' => 'Open',
            'featured' => false,
        ]);

        $job4 = JobListing::create([
            'employer_id' => $emp2->id,
            'category_id' => $cat3->id,
            'title' => 'Database Administrator & MySQL Specialist',
            'company' => 'Innovate PK',
            'location' => 'Karachi, Pakistan',
            'type' => 'Contract',
            'salary_range' => 'PKR 140,000 - 190,000 / month',
            'experience_level' => 'Mid Level',
            'description' => 'Responsible for maintaining database uptime, backup automated scripts, and query optimization.',
            'requirements' => "- Deep knowledge of MySQL, PostgreSQL, backup strategies, and replication.",
            'status' => 'Open',
            'featured' => false,
        ]);

        // 6. Create Applications
        $app1 = Application::create([
            'job_id' => $job1->id,
            'job_seeker_id' => $seeker1->id,
            'cover_letter' => 'Dear TechCorp Hiring Manager, I am a dedicated Laravel developer with extensive experience building full-stack web applications. I would love to contribute to your core backend engineering team.',
            'resume_url' => 'https://github.com/zeeshan',
            'status' => 'Shortlisted',
        ]);

        $app2 = Application::create([
            'job_id' => $job3->id,
            'job_seeker_id' => $seeker2->id,
            'cover_letter' => 'Hi Innovate PK Team, I have 4+ years of UX research and visual UI design experience. I am thrilled to apply for the Lead Designer position.',
            'resume_url' => 'https://linkedin.com/in/aminakhan',
            'status' => 'Pending',
        ]);

        // 7. Create Notifications
        Notification::create([
            'user_id' => $seeker1->id,
            'title' => 'Application Shortlisted!',
            'message' => 'Congratulations! TechCorp Solutions shortlisted your application for Senior Laravel Backend Engineer.',
            'is_read' => false,
            'link' => route('dashboard'),
        ]);

        Notification::create([
            'user_id' => $emp1->id,
            'title' => 'New Applicant Received',
            'message' => 'Zeeshan Ahmed Siddiq applied for Senior Laravel Backend Engineer.',
            'is_read' => true,
            'link' => route('dashboard'),
        ]);
    }
}
