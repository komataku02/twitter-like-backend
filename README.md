# SHARE – Backend (Laravel API)

Twitter風SNS の API サーバです。  
フロントエンド（Nuxt 4）からのリクエストに応じ、投稿・いいね・コメント等を提供します。

## 📦 スタック
- **PHP 8.2+**
- **Laravel 10/11**（プロジェクト作成時の最新版）
- DB：SQLite / MySQL / PostgreSQL（開発は SQLite 推奨）
- 認証：Firebase Authentication をフロントで実施 → バックはトークン検証/ユーザ同期を実装予定

## 🏗 ディレクトリ構成（抜粋）
backend/
├─ app/
├─ config/
├─ database/
├─ routes/
│ └─ api.php # /api/v1 配下のルーティング
└─ .env.example


## 🚀 セットアップ（ローカル）

### 前提
- PHP **8.2 以上**
- Composer **2 以上**
- Node（フロント用、API側では必須ではない）

### 1) 依存関係インストール
```bash
composer install

2) 環境変数
cp .env.example .env
php artisan key:generate

.env の例（必要に応じて編集）：
APP_NAME=SHARE
APP_ENV=local
APP_KEY= # ← php artisan key:generate で自動設定
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# 開発は SQLite が簡単です（DB ファイルを用意）
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/backend/database/database.sqlite

# CORS 設定（config/cors.php 側にも反映）
# Nuxt 側が http://localhost:3000 を使うため、そこからのアクセスを許可

SQLite を使う場合（簡単）：
# DBファイルを作る（無ければ）
touch database/database.sqlite

# マイグレーション（後でテーブル定義を追加）
php artisan migrate

3) サーバ起動
php artisan serve --host=127.0.0.1 --port=8000
# http://127.0.0.1:8000

🌐 エンドポイント（現状）

GET /api/v1/health … ヘルスチェック（例）
{ "status": "ok", "timestamp": "2025-08-18T08:45:33.126Z" }

routes/api.php（抜粋）：
Route::prefix('v1')->group(function () {
    Route::get('/health', fn() => response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]));
});

🔐 CORS 設定

フロントからのアクセスを許可するため、config/cors.php を調整します。
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => false,

変更後は php artisan config:clear をお忘れなく。

