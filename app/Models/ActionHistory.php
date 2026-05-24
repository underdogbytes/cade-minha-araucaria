<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionHistory extends Model
{
    use HasUuids;

    // Disable updated_at since only created_at is needed
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action_type',
        'reward_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
