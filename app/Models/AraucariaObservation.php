<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AraucariaObservation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'photo_path',
        'stage',
        'gender',
        'is_shared',
        'observed_at'
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'is_shared' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AraucariaObservationReport::class, 'araucaria_observation_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(AraucariaObservationUpdate::class, 'araucaria_observation_id')->latest();
    }
}
