<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'min:7',
                'max:20',
            ],
            'role' => [
                'sometimes',
                'string',
                Rule::in(User::getAvailableRoles()),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no puede exceder :max caracteres.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'phone.regex' => 'El formato del teléfono no es válido.',
            'phone.min' => 'El teléfono debe tener al menos :min caracteres.',
            'phone.max' => 'El teléfono no puede exceder :max caracteres.',
            'role.in' => 'El rol seleccionado no es válido.',
        ];
    }

    /**
     * Get the user data for registration.
     */
    public function getUserData(): array
    {
        $data = $this->only(['name', 'email', 'password', 'phone']);

        // Set default role if not provided
        $data['role'] = $this->input('role', User::ROLE_CUSTOMER);

        return $data;
    }
}
