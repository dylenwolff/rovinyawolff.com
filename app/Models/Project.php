<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'title', 'slug', 'category', 'type', 'summary', 'url', 'image_path', 'pdf_path', 'client', 'organization', 'status', 'published_at',
    'challenge', 'solution', 'responsibilities', 'technologies', 'gallery', 'accent',
    'is_featured', 'is_downloadable', 'sort_order', 'is_published',
])]
class Project extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'gallery' => 'array',
            'published_at' => 'date',
            'is_featured' => 'boolean',
            'is_downloadable' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
