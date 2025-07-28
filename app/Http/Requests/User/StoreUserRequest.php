<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender_id' => 'required|integer|exists:gender,id',
            'document_type_id' => 'required|integer|exists:document_type,id',
            'document_number' => 'required|integer|unique:users,document_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telephone' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',

            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser una cadena de texto',
            'last_name.max' => 'El apellido no puede tener más de 255 caracteres',

            'gender_id.required' => 'El género es requerido',
            'gender_id.integer' => 'El género debe ser un valor numérico',
            'gender_id.exists' => 'El género seleccionado no es válido',

            'document_type_id.required' => 'El tipo de documento es requerido',
            'document_type_id.integer' => 'El tipo de documento debe ser un valor numérico',
            'document_type_id.exists' => 'El tipo de documento seleccionado no es válido',

            'document_number.required' => 'El número de documento es requerido',
            'document_number.integer' => 'El número de documento debe ser numérico',
            'document_number.unique' => 'Este número de documento ya está registrado',

            'email.required' => 'El correo electrónico es requerido',
            'email.string' => 'El correo electrónico debe ser una cadena de texto',
            'email.email' => 'El correo electrónico debe tener un formato válido',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres',
            'email.unique' => 'Este correo electrónico ya está registrado',

            'telephone.required' => 'El teléfono es requerido',
            'telephone.integer' => 'El teléfono debe ser numérico',

            'address.string' => 'La dirección debe ser una cadena de texto',
            'address.max' => 'La dirección no puede tener más de 255 caracteres',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La confirmación de la contraseña no coincide',

            'password_confirmation.required_with' => 'La confirmación de la contraseña es requerida cuando se proporciona una contraseña',
            'password_confirmation.string' => 'La confirmación de la contraseña debe ser una cadena de texto',
            'password_confirmation.min' => 'La confirmación de la contraseña debe tener al menos 8 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
