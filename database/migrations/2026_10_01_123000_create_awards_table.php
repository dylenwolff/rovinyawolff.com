<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('placement')->nullable();
            $table->string('issuer');
            $table->date('awarded_at')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('proof_url')->nullable();
            $table->string('recognition_for')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('awards')->insert([
            [
                'title' => 'Most Outstanding Digital Transformation Champion',
                'placement' => 'Winner',
                'issuer' => 'Leo District 306 C1',
                'awarded_at' => '2025-06-14',
                'description' => 'Personal recognition for leading practical digital transformation initiatives within the Leo movement.',
                'proof_url' => 'https://kolonnawaleos.lk/wp-content/uploads/2025/09/250930-witness-september-2025.pdf',
                'recognition_for' => 'Personal recognition',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Best Leo Club Website',
                'placement' => '1st Runner-Up',
                'issuer' => 'Leo District 306 C1',
                'awarded_at' => '2025-06-14',
                'description' => 'Recognition for the design, development, and management of kolonnawaleos.lk.',
                'proof_url' => 'https://kolonnawaleos.lk/wp-content/uploads/2025/09/250930-witness-september-2025.pdf',
                'recognition_for' => 'kolonnawaleos.lk',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'TopWeb.LK Award',
                'placement' => 'March 2025 Winner',
                'issuer' => 'TopWeb.LK by LK Domain Registry',
                'awarded_at' => '2025-03-31',
                'description' => 'Awarded to kolonnawaleos.lk for meeting the quality standards of the monthly TopWeb.LK programme.',
                'proof_url' => 'https://topweb.lk/winners/kolonnawaleos/',
                'recognition_for' => 'kolonnawaleos.lk',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
