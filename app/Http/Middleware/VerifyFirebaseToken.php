<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class VerifyFirebaseToken
{
    public function __construct(private FirebaseAuth $firebaseAuth) {}

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $verified = $this->firebaseAuth->verifyIdToken($token);
            $claims = $verified->claims();
            $uid   = (string) $claims->get('sub');
            $email = $claims->get('email');
            $name  = $claims->get('name');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // ★ usersテーブルに username(必須) と firebase_uid(UNIQUE) がある前提
        $user = User::firstOrCreate(
            ['firebase_uid' => $uid],
            [
                'name'      => $name ?: ('user_' . substr($uid, 0, 6)),
                'username'  => 'user_' . substr($uid, 0, 6), // ← 追加必須
                'email'     => $email ?: ($uid . '@local.invalid'),
                'password'  => Hash::make(Str::random(40)), // ダミー
            ]
        );

        // 認証ユーザーとして扱えるようにセット（APIはstatelessでもOK）
        auth()->setUser($user);
        // コントローラから取り出しやすいようにリクエスト属性にも格納
        $request->attributes->set('auth_user', $user);

        return $next($request);
    }
}
