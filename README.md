# SHARE – Backend (Laravel API)

Twitter風SNS「SHARE」のバックエンドです。  
Laravel を用いて REST API を提供し、フロントエンド（Nuxt 4）と連携して投稿・いいね・コメント等を扱います。

< --- トップ画面（APIの説明用画像やSwagger等があれば） ---- >

---

## 作成した目的
- フロント（Nuxt）×バック（Laravel）のフルスタック構成を学習・実践するため  
- 認証・バリデーション・API 設計など、実務で必要な基礎を身につけるため  
- ポートフォリオとして SNS 風アプリのバックエンド実装を提示するため

---

## アプリケーションURL
- 開発用: `http://127.0.0.1:8000`
- デプロイURL: （デプロイ後に記載）
- 注意事項: フロントからのアクセス（http://localhost:3000）を CORS で許可する必要があります

---

## 他のリポジトリ
- フロントエンド（Nuxt 4）: https://github.com/komatuku02/twitter-like-frontend

---

## 機能一覧（予定含む）
- ヘルスチェック（稼働確認）
- 投稿の作成 / 一覧 / 詳細 / 削除
- いいね（トグル）
- コメントの作成 / 一覧
- 入力バリデーション
- 認証（Firebase Authentication の ID トークン検証を今後追加予定）

---

## 使用技術（実行環境）
- PHP 8.2+  
- Laravel 10/11（プロジェクト作成時の最新版）  
- データベース: SQLite / MySQL / PostgreSQL（開発は SQLite 推奨）  
- Composer 2+  

---

## テーブル設計
< --- 作成したテーブル設計の画像 ---- >

---

## ER図
< --- 作成したER図の画像 ---- >

---

## 環境構築

### 前提
- PHP 8.2+ / Composer 2+  
- DB（SQLite 推奨）

### 1) リポジトリをクローン
```
git clone https://github.com/komataku02/twitter-like-backend.git
cd twitter-like-backend
```
### 2) 依存関係のインストール
```
composer install
```
### 3) 環境変数の設定
```
cp .env.example .env
php artisan key:generate
```
### .envの例（SQLite を使う簡易構成）
```
APP_NAME=SHARE
APP_ENV=local
APP_KEY= # ← `php artisan key:generate` で自動設定
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack

DB_CONNECTION=sqlite
# SQLite を使う場合は database/database.sqlite を作成してください

# CORS は config/cors.php を利用（フロント http://localhost:3000 を許可）
```
### 4) SQLite ファイル作成（SQLite を使う場合）
```
touch database/database.sqlite
```
### 5) マイグレーション
```
php artisan migrate
```
### 6) 開発サーバー起動
```
php artisan serve --host=127.0.0.1 --port=8000
```
ブラウザまたは cURL で http://127.0.0.1:8000/api/v1/health にアクセスして応答を確認します。

## エンドポイント
### ヘルスチェック
・GET/api/v1/health
・レスポンス例
```
{ "status": "ok", "timestamp": "2025-08-18T08:45:33.126Z" }
```
・ルーティング
```
Route::prefix('v1')->group(function () {
    Route::get('/health', [HealthCheckController::class, 'index']);
});
```
・controller
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
        ]);
    }
}
```
・動作確認コマンド
```
curl -s http://127.0.0.1:8000/api/v1/health | jq .
```
## CORS 設定（開発）
config/cors.phpを編集し、フロントのオリジンを許可してください。
```
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3000'],
'allowed_headers' => ['*'],
'supports_credentials' => false,
```
反映:
```
php artisan config:clear
```