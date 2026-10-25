<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'contact_id', 'lead_id', 'recipient_name', 'recipient_email', 'subject', 'body', 'status', 'sent_at', 'follow_up_at', 'external_message_id', 'notes'])]
class OutreachEmail extends Model
{
    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'follow_up_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
