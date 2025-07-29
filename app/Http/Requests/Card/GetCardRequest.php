<?php

namespace App\Http\Requests\Card;

use App\Rules\CheckCardExistence;
use Illuminate\Foundation\Http\FormRequest;

class GetCardRequest extends FormRequest
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
            'crd_intsnr' => ['required', new CheckCardExistence(2)],
            //
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'crd_intsnr' => $this->route('crd_intsnr'),
        ]);
    }
    public function messages(): array
    {
        return [
            'crd_intsnr.required' => 'El número de serie de la tarjeta es requerido',
            'crd_intsnr.exists' => 'La tarjeta con el número de serie proporcionado no existe',
        ];
    }
}
