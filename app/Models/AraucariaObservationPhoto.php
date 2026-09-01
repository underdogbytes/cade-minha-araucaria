<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AraucariaObservationPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'araucaria_observation_id',
        'photo_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function observation(): BelongsTo
    {
        return $this->belongsTo(AraucariaObservation::class, 'araucaria_observation_id');
    }
}
