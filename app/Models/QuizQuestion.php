<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'question_text',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class);
    }
}
