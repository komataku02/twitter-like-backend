<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('grapheme_max', function ($attribute, $value, $parameters) {
            $max = (int)($parameters[0] ?? 120);
            $text = trim((string) $value);
            if (function_exists('grapheme_strlen')) {
                $len = grapheme_strlen($text);
            } elseif (function_exists('mb_strlen')) {
                $len = mb_strlen($text, 'UTF-8');
            } else {
                $len = strlen($text);
            }
            return $len <= $max;
        }, ':attribute は :max 文字以内で入力してください。');

        // :max を数値に置換
        Validator::replacer('grapheme_max', function ($message, $attribute, $rule, $parameters) {
            $max = (int)($parameters[0] ?? 0);
            return str_replace(':max', (string) $max, $message);
        });
    }
}
