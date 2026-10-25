<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['contact_id', 'organization_id', 'title', 'source', 'status', 'estimated_value', 'description', 'next_follow_up_at', 'converted_at'])]
class Lead extends Model
{
    protected function casts(): array
    {
        return ['estimated_value' => 'decimal:2', 'next_follow_up_at' => 'datetime', 'converted_at' => 'datetime'];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function outreachEmails(): HasMany
    {
        return $this->hasMany(OutreachEmail::class);
    }
}
