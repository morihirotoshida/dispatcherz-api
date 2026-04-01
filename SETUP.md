# dispatcher-api インストール手順と設定方法

**dispatcherZ** の裏側でデータベース（MySQL）と通信し、配車データや顧客情報を管理・提供する Laravel 製のバックエンドAPIサーバーです。

---

## 1. システム要件（前提条件）
本APIを動作させるには、以下の環境が必要です。
* **PHP:** 8.1 以上（`php-mysql`, `php-mbstring`, `php-xml`, `php-curl`, `php-zip` などの拡張モジュールが必要）
* **Composer:** 最新版
* **MySQL:** 8.0 以上（または MariaDB）
* **Git**

---

## 2. インストール手順

### ① ソースコードの取得
コマンドライン（ターミナル / コマンドプロンプト / PowerShell）を開き、GitHubからソースコードをダウンロードします。

~~~bash
git clone https://github.com/morihirotoshida/dispatcher-api.git
cd dispatcher-api
~~~

### ② 依存パッケージのインストール
Composerを使用して、Laravelが動作するために必要なパッケージ群をインストールします。

~~~bash
composer install
~~~

### ③ 環境変数（.env）ファイルの設定
設定のひな形ファイル（`.env.example`）をコピーして、ご自身の環境用の設定ファイル（`.env`）を作成します。

~~~bash
# Linux / Mac / Git Bash の場合
cp .env.example .env

# Windows コマンドプロンプトの場合
copy .env.example .env
~~~

作成された `.env` ファイルをテキストエディタ等で開き、データベースの接続情報を環境に合わせて書き換えます。

~~~env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dispatcherz_db    # 作成したデータベース名
DB_USERNAME=root              # MySQLのユーザー名（デフォルトはrootやdispatcher等）
DB_PASSWORD=                  # MySQLのパスワード（設定していれば入力）
~~~

### ④ アプリケーションキーの生成
Laravelのセキュリティ・暗号化に必要な専用キーを生成します。（`.env` ファイルに自動的に追記されます）

~~~bash
php artisan key:generate
~~~

---

## 3. データベースの構築（マイグレーション）

事前にMySQL上で `dispatcherz_db` という名前のデータベースを作成しておいてください。
その後、以下のコマンドを実行して、システムに必要なテーブル（配車履歴や設定保存用テーブルなど）を自動生成します。

~~~bash
php artisan migrate
~~~
※確認メッセージが出た場合は `yes` を選択して進めてください。

---

## 4. サーバーの起動

以下のコマンドで、APIサーバーを立ち上げます。

~~~bash
php artisan serve
~~~

ターミナルに `Server running on [http://127.0.0.1:8000]` と表示されれば成功です！
APIサーバーが待機状態になりますので、**このターミナルは閉じずに開いたまま**にしておいてください。

これでフロントエンド（dispatcherZ アプリ）を起動すると、自動的にこのAPIへ通信を行い、データの保存や読み込みが行われるようになります。

---

## 5. 補足：管理者PINコードについて
本APIには、フロントエンド側で「管理者ダッシュボード」を開くための認証ロジックが含まれています。
PINコードはフロントエンドの設定画面からいつでも変更可能です。万が一PINコードを忘れてしまった場合は、データベースの `settings` テーブル等から直接リセットを行ってください。