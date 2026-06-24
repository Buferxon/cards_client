<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

abstract class ApiFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        Log::warning('API validation failed', [
            'method' => $this->method(),
            'path' => $this->path(),
            'route' => optional($this->route())->getName(),
            'ip' => $this->ip(),
            'user_id' => optional($this->user())->getAuthIdentifier(),
            'input' => $this->sanitizedInput(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function sanitizedInput(): array
    {
        return Arr::except($this->all(), [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'api_token',
        ]);
    }
}
