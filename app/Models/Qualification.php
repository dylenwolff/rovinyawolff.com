<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'institution', 'credential_type', 'start_year', 'end_year',
    'description', 'credential_url', 'is_published', 'sort_order',
])]
class Qualification extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
