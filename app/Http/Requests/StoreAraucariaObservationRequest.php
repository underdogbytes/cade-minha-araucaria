<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAraucariaObservationRequest extends FormRequest
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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo_path' => ['required_without:photos', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'photos' => ['required_without:photo_path', 'nullable', 'array', 'min:1', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'stage' => ['required', 'in:seedling,sapling,adult,dead'],
            'gender' => ['required', 'in:male,female,unknown'],
            'observed_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
    /**
     * Customiza as mensagens de erro de validação.
     */
    public function messages(): array
    {
        return [
            'photo_path.required_without' => 'Pelo menos uma foto da árvore é obrigatória.',
            'photos.required_without'     => 'Pelo menos uma foto da árvore é obrigatória.',
            'photo_path.image'            => 'O arquivo enviado deve ser uma imagem válida.',
            'photos.*.image'              => 'Cada arquivo enviado deve ser uma imagem válida.',
            'photo_path.max'              => 'A imagem não pode ser maior que 20 MB.',
            'photos.*.max'                => 'Cada imagem não pode ser maior que 20 MB.',
        ];
    }
}

