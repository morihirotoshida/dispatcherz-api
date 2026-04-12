# dispatcherZ-api
**dispatcherZ-api** は、次世代タクシー配車・顧客管理システム「dispatcherZ」のバックエンドを担う RESTful API サーバーです。
フロントエンド（Flutterアプリケーション）および CTI（着信連動）システムと連携し、顧客データや配車ステータスの高速な処理・永続化を提供します。

## ✨ 主な機能 (Features)
- **配車伝票管理 (Dispatch Management)**: 配車伝票の作成、更新、ステータス変更（未手配/配車完了/キャンセル）を行うエンドポイント。

- **顧客情報検索 (Customer Search)**: CTIシステムからの電話番号をキーにした、過去の利用履歴と顧客データの高速検索。

- **管理者認証 (Admin Authentication)**: PINコードによる管理者権限の検証システム。

- **データインポート (CSV Import)**: 大量の過去データや外部システムからのCSVデータの一括アップサート（Upsert）処理。

- **柔軟な期間検索**: ダッシュボードの要件に応じた、期間指定（カレンダー）による配車履歴データの抽出。

## 🛠 技術スタック (Tech Stack)
- **Framework**: Laravel (PHP)

- **Database**: MySQL

- **Data Format**: JSON (REST API)  

バックエンドのフレームワーク「Laravel」設定は **README_LARAVEL.md** を参照してください。

## 🚀 環境構築と起動 (Installation & Setup)
ローカル開発環境または本番サーバーでAPIを稼働させるための基本手順です。
（※Zorin OSなどでの自動起動スクリプトを利用する場合は、手引書をご参照ください）

### 1. リポジトリの準備と依存パッケージのインストール
```Bash
composer install
```
### 2. 環境変数の設定
.env.example をコピーして .env ファイルを作成し、データベース接続情報を設定します。

```Bash
cp .env.example .env
php artisan key:generate
.env の設定例 (MySQL):
```
```Ini, TOML
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
### 3. データベースのマイグレーション
テーブル構造を初期化し、構築します。（※必要に応じて --seed で初期データを投入してください）

```Bash
php artisan migrate
```
### 4. 開発サーバーの起動
デフォルトでは http://127.0.0.1:8000 でAPIが待ち受けを開始します。

```Bash
php artisan serve
```
## 📄 ライセンス (License)  
本ソフトウェアは **BSD-3-Clause License** のもとで公開されています。