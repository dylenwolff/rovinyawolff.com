<?php

namespace App\Providers;

use App\Models\Award;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Observers\OptimizeUploadedImages;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Project::observe(OptimizeUploadedImages::class);
        Award::observe(OptimizeUploadedImages::class);
        SiteSetting::observe(OptimizeUploadedImages::class);
    }
}
