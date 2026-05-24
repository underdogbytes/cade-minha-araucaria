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
        'photo_url',
        'stage',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
