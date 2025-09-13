<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'body' => ['required', 'string','maz:280'],
            'images' => ['nullable','array','maz:4'],
            'images.*' => ['file','image','mimes:jpeg,jpg,png,webp,gif','maz:5120'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $hasBody = filled($this->input('body'));
            $hasFiles = is_array($this->file('images')) && count($this->file('images')) > 0;
            if (!$hasBody && !$hasFiles) {
                $v->errors()->add('body','本文または画像のどちらか1つは必須です。');
            }
        });
    }
}
