<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAraucariaObservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latitude'  => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'photo_path' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'stage'     => ['sometimes', 'in:seedling,sapling,adult,dead'],
            'gender'    => ['sometimes', 'in:male,female,unknown'],
            'observed_at' => ['sometimes', 'date'],
        ];
    }
}