<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('url');
            $table->string('client')->nullable()->after('image_path');
            $table->string('status')->nullable()->after('client');
            $table->text('challenge')->nullable()->after('status');
            $table->text('solution')->nullable()->after('challenge');
            $table->text('responsibilities')->nullable()->after('solution');
            $table->json('technologies')->nullable()->after('responsibilities');
            $table->boolean('is_featured')->default(false)->after('accent');
        });

        DB::table('projects')->where('sort_order', 1)->update([
            'title' => 'EduPoint: One platform for modern institutes',
            'category' => 'Education Management SaaS',
            'summary' => 'A commercially ready platform that brings students, teachers, classes, attendance, payments and communication into one secure, branded workspace.',
            'url' => 'https://edupoint.skrepkie.com',
            'image_path' => '/images/projects/edupoint-homepage.jpg',
            'client' => 'Skrepkie product · Institute of Izoid pilot',
            'status' => 'Commercially ready · Pilot implementation',
            'challenge' => 'Many education providers rely on paper records, spreadsheets, WhatsApp conversations and manual payment confirmations. Information becomes fragmented, work is repeated, and administrators lack one clear view of their institute.',
            'solution' => 'I designed EduPoint as a multi-institute platform where each organisation receives its own secure, branded workspace. It centralises student and teacher management, branches, classes, scheduling, attendance, payments, subscriptions and communication while keeping every institute’s information isolated.',
            'responsibilities' => 'I independently handled the complete product lifecycle: product planning, system architecture, user experience, database design, frontend and backend development, security, automated domains and SSL, email delivery, deployment, monitoring and ongoing maintenance.',
            'technologies' => json_encode(['Laravel', 'PHP', 'MySQL', 'Tailwind CSS', 'Alpine.js', 'Nginx', 'Ubuntu']),
            'is_featured' => true,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'client', 'status', 'challenge', 'solution', 'responsibilities', 'technologies', 'is_featured']);
        });
    }
};
