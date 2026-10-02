<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'placement', 'issuer', 'awarded_at', 'description', 'image_path',
    'proof_url', 'recognition_for', 'is_featured', 'is_published', 'sort_order',
])]
class Award extends Model
{
    protected function casts(): array
    {
        return [
            'awarded_at' => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
