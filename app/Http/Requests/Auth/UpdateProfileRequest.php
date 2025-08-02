<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    use HandlesValidationErrors;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            ],
            'email' => [
                'sometimes',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'min:7',
                'max:20',
            ],
            'avatar' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                'url',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no puede exceder :max caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'phone.regex' => 'El formato del teléfono no es válido.',
            'phone.min' => 'El teléfono debe tener al menos :min caracteres.',
            'phone.max' => 'El teléfono no puede exceder :max caracteres.',
            'avatar.url' => 'El avatar debe ser una URL válida.',
            'avatar.max' => 'La URL del avatar no puede exceder :max caracteres.',
        ];
    }

    /**
     * Get the profile data to update.
     */
    public function getProfileData(): array
    {
        return $this->only(['name', 'email', 'phone', 'avatar']);
    }
}
