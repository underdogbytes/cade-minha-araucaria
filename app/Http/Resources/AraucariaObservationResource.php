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
            'created_at' => $this->created_at?->toIso8601String(),
            'observed_at' => $this->observed_at?->toIso8601String(),
        ];
    }
}
