<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 認可は firebase ミドルウェア側でやっているので true でOK
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:50'],
            'username' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '表示名',
            'username' => 'ユーザー名',
            'bio' => '自己紹介',
        ];
    }
}
