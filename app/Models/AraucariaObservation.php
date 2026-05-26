<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AraucariaObservation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'photo_path',
        'stage',
        'gender',
        'observed_at'
    ];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
