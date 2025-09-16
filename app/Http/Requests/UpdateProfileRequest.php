<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = auth()->id() ?? $this->user()?->id; // 念のための両対応

        return [
            'username' => [
                'required','string','max:20',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'name' => ['sometimes', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'ユーザー名は必須です',
            'username.max' => 'ユーザー名は20文字以内で入力してください',
            'username.unique' => 'このユーザー名は既に使われています',
        ];
    }
}
