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
        'observed_at',
        'votes_normal_count',
        'votes_doente_count',
        'votes_risco_count',
        'votes_protegida_count',
        'votes_morta_count',
        'computed_status',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'is_shared' => 'boolean',
        'votes_normal_count' => 'integer',
        'votes_doente_count' => 'integer',
        'votes_risco_count' => 'integer',
        'votes_protegida_count' => 'integer',
        'votes_morta_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AraucariaObservationPhoto::class, 'araucaria_observation_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AraucariaObservationReport::class, 'araucaria_observation_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(AraucariaObservationUpdate::class, 'araucaria_observation_id')->latest();
    }

    public function statusVotes(): HasMany
    {
        return $this->hasMany(ObservationStatusVote::class, 'araucaria_observation_id');
    }

    /**
     * Recalcula o status predominante ou empate (bicolor) conforme votos rápidos da comunidade.
     * Regras:
     * - Se morta for a mais votada, é morta (não pode ser normal, nem protegida, nem em risco).
     * - Só pode ser normal se não estiver doente nem em risco.
     * - Árvore pode ser normal e protegida, ou doente e em risco.
     * - Em caso de empate entre os mais votados, retorna a combinação composta (ex: doente_risco, normal_protegida).
     */
    public function recalculateStatus(): string
    {
        $normal = (int) $this->votes_normal_count;
        $doente = (int) $this->votes_doente_count;
        $risco = (int) $this->votes_risco_count;
        $protegida = (int) $this->votes_protegida_count;
        $morta = (int) $this->votes_morta_count;

        $total = $normal + $doente + $risco + $protegida + $morta;
        if ($total === 0) {
            $status = ($this->stage === 'dead') ? 'morta' : 'normal';
            $this->computed_status = $status;
            $this->saveQuietly();
            return $status;
        }

        $max = max($normal, $doente, $risco, $protegida, $morta);

        // Se 'morta' atingiu a maior votação: Morta é absoluta
        if ($morta > 0 && $morta === $max) {
            $this->computed_status = 'morta';
            $this->saveQuietly();
            return 'morta';
        }

        // Analisa empates entre os maiores votos não-morta
        $top = [];
        foreach (['doente' => $doente, 'risco' => $risco, 'protegida' => $protegida, 'normal' => $normal] as $key => $count) {
            if ($count === $max && $count > 0) {
                $top[] = $key;
            }
        }

        // Se 'normal' empatou com 'doente' ou 'risco', 'normal' é desclassificada:
        if (in_array('normal', $top, true) && (in_array('doente', $top, true) || in_array('risco', $top, true))) {
            $top = array_values(array_filter($top, fn($s) => $s !== 'normal'));
        }

        // Regra: não é possível estar protegida e em risco ao mesmo tempo ('risco' prevalece como alerta)
        if (in_array('protegida', $top, true) && in_array('risco', $top, true)) {
            $top = array_values(array_filter($top, fn($s) => $s !== 'protegida'));
        }

        if (count($top) === 1) {
            $status = $top[0];
        } elseif (count($top) >= 2) {
            if (in_array('doente', $top, true) && in_array('risco', $top, true)) {
                $status = 'doente_risco';
            } elseif (in_array('normal', $top, true) && in_array('protegida', $top, true)) {
                $status = 'normal_protegida';
            } elseif (in_array('protegida', $top, true) && in_array('doente', $top, true)) {
                $status = 'protegida_doente';
            } else {
                $status = implode('_', array_slice($top, 0, 2));
            }
        } else {
            $status = 'normal';
        }

        $this->computed_status = $status;
        $this->saveQuietly();
        return $status;
    }
}

