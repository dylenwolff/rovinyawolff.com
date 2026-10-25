<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->after('industry')->index();
        });

        $countries = [
            'Sri Lanka' => [
                'Sri Lanka Association for the Advancement of Science',
                'Sri Lanka Library Association',
                'Sri Lanka Association for Laboratory Animal Science',
                'Sri Lanka Association for Improving Higher Education Effectiveness',
                'Animals’ Welfare and Protection Association Sri Lanka',
            ],
            'Australia' => [
                'Another Colour',
                'Australian Environmental Grantmakers Network',
                'Women in Media Australia',
            ],
            'Singapore' => [
                'Flash Concepts',
                'Creativeans',
                'ACRE Design',
                'Singapore Association for Counselling',
                'National Arthritis Foundation Singapore',
            ],
            'United Arab Emirates' => [
                'Moloobhoy & Brown',
                'Engage Group',
                'frex.digital',
                'AIPPI UAE',
            ],
            'United Kingdom' => [
                'Mobas',
                'Creative UK',
                'Assembly London',
                'Buckley Creative',
                'Pearlfisher',
                'Tonik Associates',
                'AgencyUK',
                'monopo London',
                'Agency S/R',
            ],
        ];

        foreach ($countries as $country => $organizations) {
            DB::table('organizations')->whereIn('name', $organizations)->update(['country' => $country]);
        }
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex(['country']);
            $table->dropColumn('country');
        });
    }
};
