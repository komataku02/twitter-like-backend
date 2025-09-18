<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // ★ 画像1枚あたりの上限(MB)を .env から可変に（既定 15MB）
        $maxMb = (int) env('POST_IMAGE_MAX_MB', 15);
        $maxKb = $maxMb * 1024;

        return [
            'content'   => ['nullable', 'string', 'max:120'],
            'images'    => ['nullable', 'array', 'max:4'],
            // ★ 'max:' は KB 指定
            'images.*'  => ['file', 'image', 'mimes:jpeg,jpg,png,webp,gif', "max:{$maxKb}"],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $content = (string) $this->input('content', '');
            $hasText = trim($content) !== '';

            $files = $this->file('images');
            $count = is_array($files) ? count($files) : ($files ? 1 : 0);
            $hasImages = $this->hasFile('images') && $count > 0;

            if (!$hasText && !$hasImages) {
                $v->errors()->add('content', '本文または画像のいずれかは必須です。');
            }
        });
    }

    public function messages(): array
    {
        return [
            'content.max'   => '本文は120文字以内で入力してください。',
            'images.array'  => '画像の送信形式が不正です。',
            'images.max'    => '画像は最大4枚までです。',
            'images.*.image' => '画像ファイルを選択してください。',
            'images.*.mimes' => '対応形式は jpeg,jpg,png,webp,gif です。',
            'images.*.max'  => '各画像は :max KB（約 ' . env('POST_IMAGE_MAX_MB', 15) . 'MB）までです。',
        ];
    }
}
