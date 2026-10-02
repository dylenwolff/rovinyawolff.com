<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default('publication')->after('category');
            $table->string('pdf_path')->nullable()->after('image_path');
            $table->string('organization')->nullable()->after('client');
            $table->date('published_at')->nullable()->after('status');
            $table->boolean('is_downloadable')->default(false)->after('is_featured');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('linkedin_url');
            $table->string('behance_url')->nullable()->after('instagram_url');
        });

        DB::table('site_settings')->update([
            'name' => 'Rovinya Wolff', 'professional_title' => 'Publication & Visual Designer',
            'availability' => 'Available for freelance projects',
            'hero_heading' => 'Thoughtful design for stories worth', 'hero_accent' => 'sharing and keeping.',
            'hero_intro' => 'I design publications, directories, posters, and social media visuals that turn information into a clear and memorable experience.',
            'about_heading' => 'Design that gives every story a place to belong.',
            'about_body' => 'My work began with publications and campaign visuals for Leo organisations. It taught me how to bring many voices, photographs, achievements, and ideas into one consistent visual story. Today I apply that same care to organisations and clients who need thoughtful, polished design.',
            'email' => 'hello@rovinyawolff.com', 'linkedin_url' => null, 'github_url' => null, 'upwork_url' => null,
            'contact_heading' => 'Have a publication or visual idea in mind?',
            'contact_body' => 'Tell me what you are creating, who it is for, and when you need it. I will help shape it into something clear, polished, and distinctly yours.',
            'updated_at' => now(),
        ]);
        DB::table('services')->delete();
        foreach ([
            ['Publication design', 'Magazines, annual reports, newsletters, and books shaped into polished reading experiences.', ['Magazines', 'Reports', 'Newsletters']],
            ['Directories and commemorative books', 'Structured, elegant publications that bring people, milestones, and organisational stories together.', ['Club directories', 'Souvenirs', 'Yearbooks']],
            ['Posters and campaign visuals', 'Strong visual concepts for events, announcements, awareness campaigns, and promotions.', ['Posters', 'Campaigns', 'Events']],
            ['Social media design', 'Consistent, engaging visual content created to help organisations communicate clearly online.', ['Posts', 'Stories', 'Content systems']],
        ] as $index => [$title, $description, $tags]) {
            DB::table('services')->insert(['title'=>$title,'description'=>$description,'tags'=>json_encode($tags),'sort_order'=>$index + 1,'is_published'=>true,'created_at'=>now(),'updated_at'=>now()]);
        }
        DB::table('projects')->delete();
        DB::table('awards')->delete();
        DB::table('qualifications')->delete();
    }

    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn(['type', 'pdf_path', 'organization', 'published_at', 'is_downloadable']));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn(['instagram_url', 'behance_url']));
    }
};
