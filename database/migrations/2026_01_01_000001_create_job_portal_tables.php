<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add profile & role columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('job_seeker'); // job_seeker, employer, admin
            $table->string('headline')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->string('skills')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('resume_link')->nullable();
            $table->string('avatar')->nullable();
        });

        // Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('fas fa-briefcase');
            $table->timestamps();
        });

        // Job Listings Table
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('type')->default('Full-Time'); // Full-Time, Part-Time, Contract, Remote
            $table->string('salary_range')->nullable();
            $table->string('experience_level')->default('Mid Level');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('status')->default('Open'); // Open, Closed
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });

        // Job Applications Table
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
            $table->foreignId('job_seeker_id')->constrained('users')->onDelete('cascade');
            $table->text('cover_letter');
            $table->string('resume_url')->nullable();
            $table->string('status')->default('Pending'); // Pending, Shortlisted, Rejected, Hired
            $table->timestamps();
        });

        // Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('job_listings');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'headline', 'phone', 'location', 'bio',
                'skills', 'company_name', 'company_website', 'resume_link', 'avatar'
            ]);
        });
    }
};
