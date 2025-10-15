# アプリケーション名
time-card
（coachtech 勤怠管理アプリ）

-   このリポジトリは amd 版です(Windows/IntelCPU の Mac 向け)


## 環境構築

プロジェクトをセットアップするために、以下の手順を実行してください。

1. Dockerのビルド
まず、プロジェクトのルートディレクトリに移動し、Dockerコンテナをビルドします。

```
docker compose up -d --build
```

これにより、必要なコンテナが構築されます。

2. env ファイルを作成します。

```
cp src/.env.example src/.env
```

3. php にコンテナに移動します。
```
docker compose exec php bash
```

4. シーディングの実行
composer パッケージをインストールします。

```
composer install
```

5. アプリケーションキーを作成します。
```
php artisan key:generate
```

6. マイグレーションの実行
マイグレーションを実行して、データベースの構造を作成します。

```
php artisan migrate
```

これで、必要なテーブルがデータベースに作成されます。

7. シーディングの実行
もし必要であれば、シーディングも実行します。シーディングは、サンプルデータをデータベースに挿入するための処理です。

```
php artisan db:seed
```

シーディング後、サンプルデータがデータベースに挿入されます。



## 使用技術(実行環境)

このプロジェクトでは、以下の技術を使用しています。

・Laravel 8.x: PHPフレームワークで、アプリケーションのバックエンドロジックを処理します。

・Docker: コンテナ化された開発環境を提供し、依存関係やサーバー設定を簡素化します。

・MySQL: アプリケーションのデータベースとして使用しています。

・PHP 7.4: Laravelを実行するために使用するPHPのバージョン。

・Nginx: Webサーバーとして使用され、リクエストを処理します。

・Composer: PHPのパッケージマネージャーとして、Laravelの依存関係を管理します。


📬 メール送信のテスト環境（Mailtrap）

このアプリは、開発中のメール送信確認に Mailtrap
 を使用しています。

🔧 Mailtrapの設定手順

1 https://mailtrap.io/
 に登録・ログイン

2 「My Sandbox」Inbox を開く

3 Integrations または Code Samples タブから「laravel 7.x and 8.x」を選択

4 表示された内容を .env に追加：
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。　
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。
　
5 .env を保存したら、下記コマンドで反映：

```
php artisan config:clear
```

テストアカウント-----------------------

name: 管理者（管理者ユーザー）
email: test@example.com
password: password

-----------------------------------------

## ER図

<img src="ER.png" alt="ERimg">



## URL

・開発環境：http://localhost/

・phpMyAdmin:：http://localhost:8080/
