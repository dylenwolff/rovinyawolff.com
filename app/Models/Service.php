<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'tags', 'sort_order', 'is_published'])]
class Service extends Model
{
    protected function casts(): array
    {
        return ['tags' => 'array', 'is_published' => 'boolean'];
    }
}
