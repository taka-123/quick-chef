# Quick Chef プロジェクト

## ビジョンとゴール

### ビジョン
食材の写真から瞬時にレシピを提案し、料理の意思決定を支援するAIアプリケーション

### プロジェクトゴール
1. 高精度な食材画像認識システムの構築
2. 認識した食材に基づく適切なレシピ提案
3. ユーザーフレンドリーなインターフェースの提供
4. リアルタイムでの高速処理

## アーキテクチャ

### システム構成
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Frontend      │────▶│    Backend      │────▶│  External APIs  │
│   (Nuxt.js)     │     │   (Laravel)     │     │  (OpenAI)       │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                       │
         └───────┬───────────────┘
                 │
         ┌───────▼────────┐
         │   PostgreSQL   │
         │   (SQLite)     │
         └────────────────┘
```

### 主要コンポーネント
1. **フロントエンド**: Nuxt.js 3によるSPA
2. **バックエンド**: Laravel 12によるRESTful API
3. **画像認識**: OpenAI Vision API
4. **データベース**: PostgreSQL (開発時はSQLite)
5. **非同期処理**: Laravel Queue + Jobs

## 技術スタック

### バックエンド
- **フレームワーク**: Laravel 12
- **言語**: PHP 8.3
- **認証**: JWT (php-open-source-saver/jwt-auth)
- **HTTPクライアント**: Guzzle
- **キュー**: Laravel Queue (database driver)

### フロントエンド
- **フレームワーク**: Nuxt.js 3
- **UIライブラリ**: Vuetify 3
- **状態管理**: Pinia
- **HTTPクライアント**: Axios

### インフラ・ツール
- **コンテナ**: Docker & Docker Compose
- **開発環境**: Laravel Sail
- **CI/CD**: GitHub Actions (予定)
- **デプロイ**: Fly.io

### 外部サービス
- **画像認識**: OpenAI Vision API (gpt-4o-mini)
- **エラー監視**: Sentry

## 開発規約

### コーディング規約
- PSR-12準拠（PHP）
- ESLint + Prettier（JavaScript/TypeScript）
- コミットメッセージは日本語で記述

### API設計
- RESTful原則に従う
- レスポンスはJSON形式
- エラーハンドリングは統一形式

### セキュリティ
- 環境変数による機密情報管理
- CORS設定の適切な管理
- 画像アップロードサイズ制限（10MB）

## セッションルール

#今週は食材認識精度の向上に集中
#パフォーマンスより機能完成優先