<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lead_id', 'contact_id', 'organization_id', 'name', 'stage', 'amount', 'probability', 'expected_close_date', 'description', 'lost_reason'])]
class Opportunity extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'expected_close_date' => 'date'];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
