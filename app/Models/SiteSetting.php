<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'professional_title', 'availability', 'hero_heading', 'hero_accent',
    'hero_intro', 'about_heading', 'about_body', 'email', 'linkedin_url',
    'instagram_url', 'behance_url', 'github_url', 'upwork_url', 'contact_heading', 'contact_body', 'profile_photo',
])]
class SiteSetting extends Model
{
    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'name' => 'Rovinya Wolff',
            'professional_title' => 'Publication & Visual Designer',
            'availability' => 'Available for freelance projects',
            'hero_heading' => 'Thoughtful design for stories worth',
            'hero_accent' => 'sharing and keeping.',
            'hero_intro' => 'I design publications, directories, posters, and social media visuals that turn information into a clear and memorable experience.',
            'about_heading' => 'Design that gives every story a place to belong.',
            'about_body' => 'My work began with publications and campaign visuals for Leo organisations. It taught me how to bring many voices, photographs, achievements, and ideas into one consistent visual story. Today I apply that same care to organisations and clients who need thoughtful, polished design.',
            'email' => 'hello@rovinyawolff.com',
            'contact_heading' => 'Have a publication or visual idea in mind?',
            'contact_body' => 'Tell me what you are creating, who it is for, and when you need it. I will help shape it into something clear, polished, and distinctly yours.',
        ]);
    }
}
