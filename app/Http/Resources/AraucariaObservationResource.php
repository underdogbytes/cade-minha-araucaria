<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
class AraucariaObservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $formatPhoto = function (?string $path) {
            if (!$path) return null;
            if (str_starts_with($path, 'data:') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            return Storage::disk('public')->url($path);
        };

        $photosList = [];
        if ($this->relationLoaded('photos') && $this->photos->isNotEmpty()) {
            foreach ($this->photos as $photo) {
                $photosList[] = [
                    'id' => $photo->id,
                    'url' => $formatPhoto($photo->photo_path),
                    'is_primary' => (bool) $photo->is_primary,
                ];
            }
        } elseif ($this->photo_path) {
            $photosList[] = [
                'id' => null,
                'url' => $formatPhoto($this->photo_path),
                'is_primary' => true,
            ];
        }

        return [
            'id' => $this->id,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'photo_path' => $formatPhoto($this->photo_path),
            'photos' => $photosList,
            'stage' => $this->stage,
            'gender' => $this->gender,
            'observer' => $this->user ? ($this->user->username ?? $this->user->name) : 'Desconhecido',
            'is_shared' => (bool) $this->is_shared,
            'computed_status' => $this->computed_status ?: ($this->stage === 'dead' ? 'morta' : 'normal'),
            'votes' => [
                'normal' => (int) ($this->votes_normal_count ?? 0),
                'doente' => (int) ($this->votes_doente_count ?? 0),
                'risco' => (int) ($this->votes_risco_count ?? 0),
                'protegida' => (int) ($this->votes_protegida_count ?? 0),
                'morta' => (int) ($this->votes_morta_count ?? 0),
            ],
            'user_votes' => $request->user() && $this->relationLoaded('statusVotes')
                ? $this->statusVotes->where('user_id', $request->user()->id)->pluck('status')->values()->all()
                : [],
            'created_at' => $this->created_at?->toIso8601String(),
            'observed_at' => $this->observed_at?->toIso8601String(),
        ];
    }
}
