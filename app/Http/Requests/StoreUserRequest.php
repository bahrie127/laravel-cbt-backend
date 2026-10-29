<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|min:3',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'roles' => 'required|in:ADMIN,STAFF,USER',
            'password' => 'required|string|min:8',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama', 'phone' => 'nomor HP', 'roles' => 'peran', 'password' => 'kata sandi'];
    }
}
