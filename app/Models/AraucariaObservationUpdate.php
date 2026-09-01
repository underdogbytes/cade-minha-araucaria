<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AraucariaObservationUpdate extends Model
{
    protected $fillable = [
        'araucaria_observation_id',
        'user_id',
        'photo_path',
        'notes',
        'stage',
        'observed_at',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function observation(): BelongsTo
    {
        return $this->belongsTo(AraucariaObservation::class, 'araucaria_observation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
