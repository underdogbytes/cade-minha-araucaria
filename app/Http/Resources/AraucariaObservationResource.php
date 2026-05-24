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
        return [
            'id' => $this->id,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'photo_path' => Storage::disk('public')->url($this->photo_path),
            'stage' => $this->stage,
            'gender' => $this->gender,
            'observer' => $this->user->username,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
