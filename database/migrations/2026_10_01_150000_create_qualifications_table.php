<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('institution');
            $table->string('credential_type')->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->text('description')->nullable();
            $table->string('credential_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('qualifications')->insert([
            [
                'title' => 'MSc in Cyber Security',
                'institution' => 'University of Wolverhampton, United Kingdom',
                'credential_type' => 'Postgraduate degree',
                'start_year' => null,
                'end_year' => 2026,
                'description' => 'Advanced study in cybersecurity, supporting a practical interest in social engineering, online scams, secure systems, and digital safety.',
                'credential_url' => null,
                'is_published' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'PCJT Software Engineer (III)',
                'institution' => 'Java Institute for Advanced Technology',
                'credential_type' => 'SCQF Level 9',
                'start_year' => 2016,
                'end_year' => 2021,
                'description' => 'A software engineering qualification at SCQF Level 9, covering application development, systems thinking, and professional software practices.',
                'credential_url' => null,
                'is_published' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Diploma in Hardware and Network Engineering',
                'institution' => 'London Business School',
                'credential_type' => 'Diploma',
                'start_year' => null,
                'end_year' => 2011,
                'description' => 'Foundational training in computer hardware, networking, troubleshooting, and technical support.',
                'credential_url' => null,
                'is_published' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Microsoft Certified: Power Platform Fundamentals',
                'institution' => 'Microsoft',
                'credential_type' => 'Professional certification',
                'start_year' => null,
                'end_year' => 2022,
                'description' => 'Certification in the core capabilities and business value of Microsoft Power Platform.',
                'credential_url' => null,
                'is_published' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('site_settings')->update([
            'contact_body' => 'Tell me what you are trying to accomplish. I will help you find a practical way forward, even if the answer is simpler than expected.',
        ]);
        DB::table('projects')->where('slug', 'edupoint-one-platform-for-modern-institutes')->update([
            'title' => 'EduPoint: One platform for modern institutes',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
