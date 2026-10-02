<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('professional_title');
            $table->string('availability')->nullable();
            $table->string('hero_heading');
            $table->string('hero_accent');
            $table->text('hero_intro');
            $table->string('about_heading');
            $table->text('about_body');
            $table->string('email');
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('upwork_url')->nullable();
            $table->string('contact_heading');
            $table->text('contact_body');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->text('summary');
            $table->string('url')->nullable();
            $table->string('accent')->default('blue');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'name' => 'Dylen Andrew Wolff', 'professional_title' => 'Systems Engineer & Digital Solutions Developer',
            'availability' => 'Available for selected projects', 'hero_heading' => 'I turn ideas and everyday problems into',
            'hero_accent' => 'digital solutions that work.',
            'hero_intro' => 'From professional websites and custom business tools to reliable technology support and training, I build around what you actually need.',
            'about_heading' => 'Technology should make life and work easier.',
            'about_body' => 'I work across development, systems, infrastructure, and education. That range helps me look beyond a single tool and find the clearest, most practical solution for each client. As an IT lecturer, I also care deeply about making complex ideas understandable. The same clarity shapes how I communicate, document, and deliver every project.',
            'email' => 'hello@dylenwolff.com', 'linkedin_url' => 'https://www.linkedin.com/in/dylenaw/',
            'github_url' => 'https://github.com/dylenwolff', 'upwork_url' => 'https://www.upwork.com/freelancers/~01d1b15fc05390f9a2',
            'contact_heading' => 'Have an idea, a problem, or a project?',
            'contact_body' => 'Tell me what you are trying to accomplish. I will help you find a practical way forward, even if the answer is simpler than expected.',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('services')->insert([
            ['title' => 'Professional websites', 'description' => 'Clear, fast, mobile-friendly websites that build trust and help the right people find or contact you.', 'tags' => json_encode(['Business websites', 'Portfolios', 'Online presence']), 'sort_order' => 1, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Tools that simplify work', 'description' => 'Custom systems that reduce repetitive tasks, organise information, and make everyday operations easier.', 'tags' => json_encode(['Business tools', 'Automation', 'Dashboards']), 'sort_order' => 2, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Reliable technology setup', 'description' => 'Practical help with hosting, servers, deployments, email, networks, and the technology behind your business.', 'tags' => json_encode(['Hosting', 'Servers', 'Support']), 'sort_order' => 3, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Technical advice without jargon', 'description' => 'Straightforward guidance when you need to choose technology, improve an existing system, or solve a difficult problem.', 'tags' => json_encode(['Planning', 'Problem solving']), 'sort_order' => 4, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Training and education', 'description' => 'Friendly, understandable technical training, mentoring, curriculum development, and knowledge transfer.', 'tags' => json_encode(['Teaching', 'Workshops', 'Mentoring']), 'sort_order' => 5, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('projects')->insert([
            ['title' => 'Digital platforms that do more', 'category' => 'Websites & Business Tools', 'summary' => 'Custom digital experiences designed around the way each client works, communicates, and plans to grow.', 'url' => null, 'accent' => 'blue', 'sort_order' => 1, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Reliable systems behind the screen', 'category' => 'Technology & Operations', 'summary' => 'Hosting, deployment, integrations, and practical improvements that keep organisations working smoothly.', 'url' => null, 'accent' => 'cyan', 'sort_order' => 2, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('site_settings');
    }
};
