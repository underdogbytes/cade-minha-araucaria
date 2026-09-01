<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AraucariaObservationReport extends Model
{
    protected $fillable = [
        'araucaria_observation_id',
        'araucaria_observation_update_id',
        'user_id',
        'reason',
        'details',
        'status',
    ];

    public function observation(): BelongsTo
    {
        return $this->belongsTo(AraucariaObservation::class, 'araucaria_observation_id');
    }

    public function updateObservation(): BelongsTo
    {
        return $this->belongsTo(AraucariaObservationUpdate::class, 'araucaria_observation_update_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
