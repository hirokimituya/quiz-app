<!--- 画像URLの変数定義 --->
[参考画像]: https://user-images.githubusercontent.com/81066421/127855688-03f6cf37-885d-4a40-8387-2501c462210f.gif
[インフラ構成図]: https://user-images.githubusercontent.com/81066421/127653180-42854273-0623-4aa8-8bb0-219093a9b240.png
[画面遷移図]: https://user-images.githubusercontent.com/81066421/126887789-2d13a4eb-339a-4b8a-97ac-60841dc493ec.png
[鍵]: https://user-images.githubusercontent.com/81066421/120003596-dd50c580-c010-11eb-9442-5542a6466dbb.png
[ER図]: https://user-images.githubusercontent.com/81066421/127725585-f2ce6040-d3e5-4fea-b5e9-9e8f4b8348f4.png
[チェック]: https://user-images.githubusercontent.com/81066421/120052611-131d9a80-c061-11eb-9e86-f323d6cb2b41.png
[外部]: https://user-images.githubusercontent.com/81066421/120052614-13b63100-c061-11eb-8b16-a679f6241f26.png
[キー]: https://user-images.githubusercontent.com/81066421/120052616-144ec780-c061-11eb-9efc-0ab2224081ab.png


<!--- 本文はこちらから --->
# [QuizMaker](https://www.quiz-maker-simple.com)
クイズを作成・実行できるWEBアプリケーションです。<br>
自由にクイズを作成して他ユーザに回答してもらったり、他ユーザが作成したクイズを回答できます。<br>
常時SSL化に対応しており、安全にアプリを実行できるようになっております。<br>
また、高速なページ遷移を可能にするため、Vue.jsやInertia.jsを使用したSPAでアプリケーションを作成しています。<br>
（レスポンシブ対応しているため、PC・タブレット・スマートフォンからでもご確認いただけます。）
<br><br>
![参考画像][参考画像]
<br><br>

## 作成した目的
私はクイズ番組が好きなのですが、クイズを作成して共有できるアプリはあまり見たことがなかったため、自分で開発しようと思いました。<br>
シンプルなデザインで使いやすさにこだわって作りました。
<br><br>

## 使用技術
以下を使用してWEBアプリケーションを作成しました。
- フロントエンド
    - **[Vue.js](https://jp.vuejs.org/)** 2.6.1
    - **[Vuetify](https://vuetifyjs.com/ja/)** 2.4.11
    - **[Inertia.js](https://inertiajs.com/)** 0.8.7
    - **[axios](https://axios-http.com/)** 0.21.1
    - **[ESLint](https://eslint.org/)** 7.31.0
    - **[HTML/CSS](https://developer.mozilla.org/ja/docs/Web/HTML)**

- バックエンド
    - **[PHP](https://www.php.net/)** 8.0.8
    - **[Laravel](https://laravel.com/)** 8.45.1
    - **[Laravel Jetstream](https://jetstream.laravel.com/2.x/)** 2.3
    - **[Laravel Sail](https://laravel.com/docs/8.x/sail/)** 1.0.1
    - **[PHPUnit](https://phpunit.readthedocs.io/ja/latest/)** 9.5.5

- インフラ
    - **[nginx](https://www.nginx.co.jp/)** 1.20.0
    - **[CircleCI](https://circleci.com/ja/)**
    - **[Docker](https://www.docker.com/)** 20.10.7 / **[docker-compose](https://docs.docker.jp/compose/toc.html)** 1.29.2
    - **[MySQL](https://www.mysql.com/jp/)** 8.0.23 / **[phpMyAdmin](https://www.phpmyadmin.net/)** 5.1.1
    - **[AWS](https://aws.amazon.com/jp/)** (EC2, RDS, S3, ALB, CodeDeploy, ElastiCache, VPC, Route53, EIP, CertificateManager, SNS, Chatbot, IAM)
<br><br>

## 機能一覧
実装した機能は以下の通りです。
- ユーザー登録、ログイン機能
- プロフィール機能
    - 名前、メールアドレス、アイコン変更機能
    - パスワード更新機能
    - クイズ実行履歴表示設定
    - アカウント削除機能
- クイズ機能
    - クイズ作成機能
    - クイズ編集機能
    - クイズ削除機能
    - クイズ回答機能
    - クイズ回答実績表示機能
- いいね機能
- コメント機能
    - コメント投稿機能
    - コメント編集機能
    - コメント削除機能
- 検索機能
- ソート機能
- ページネーション機能
<br><br>

## インフラ構成図
インフラ構成図は以下画像の通りです。

![インフラ構成図][インフラ構成図]
<br><br>
以下インフラ構成図の補足をいたします。<br>
- 開発環境は、`Docker / docker-compose`開発環境を操作するための`Laravel Sail`を使用しています。
- 自動デプロイの流れを以下に示します。
1. featureブランチにコミットしてプルリクエストを発行すると以下の流れで自動テストが実行されます。
    1. `CircleCI`でテストジョブが走り、`PHPUnit`のテストが実施されます。
    1. `GitHub Actions`で`ESLint`が実行されます。
    1. 上記のi, iiが成功したらmasterブランチにマージ可能となります。
1. masterブランチにコミットすると以下の流れで自動テスト・ビルド・デプロイが実行されます。
    1. `CircleCI`のテストジョブが走り、`PHPUnit`のテストが実施されます。
    1. 上記のiが成功したら、`CircleCI`のビルドジョブが走り、`PHP`, `JavaScript`のビルドを実施後、`S3`にビルド結果をアップロードします。
    1. 上記のiiが成功したら、`CircleCI`から`CodeDeploy`にデプロイ開始指示を出します。
    1. `CodeDeploy`が`S3`に保存されているビルド結果を`EC2`へ展開して自動デプロイします。
    1. `CodeDeploy`と`AWS SNS`トピックと`AWS Chatbot`を連携して自動デプロイの開始と終了のタイミングでSlackへ通知します。

<br>

## 画面遷移図
画面遷移図は以下画像の通りです。<br>
> 鍵がついた黄色のエリアにあるページはログインユーザのみ遷移できるページになります。

![画面遷移図][画面遷移図]
<br><br>

## URL一覧
URLの一覧は以下表の通りです。
> 認証欄に鍵がついているURLはログインユーザのみアクセスできるページになります。

| URL                             | ルート名                        | メソッド | 認証 | 処理                                                               |
|---------------------------------|---------------------------------|:--------:|:----:|--------------------------------------------------------------------|
| /                               | home                            |    GET   |      | トップページを表示する。                                           |
| /search                         | search                          |    GET   |      | クイズ検索ページを表示する。                                       |
| /dashboard/{ユーザID}            | dashboard                       |    GET   |      | ダッシュボードページを表示する。                                   |
| /quiz/create                    | quiz.create                     |    GET   |![鍵][鍵]| クイズ作成開始ページを表示する。                                   |
| /quiz/confirm                   | quiz.create.conf                |   POST   |![鍵][鍵]| クイズ作成確認ページを表示する。                                   |
| /quiz/create                    | quiz.create                     |   POST   |![鍵][鍵]| クイズ作成処理を実行する。                                         |
| /quiz/{クイズID}                | quiz.detail                     |    GET   |      | クイズ詳細ページを表示する。                                       |
| /quiz/{クイズID}                | quiz.detail                     |  DELETE  |![鍵][鍵]| クイズを削除する。                                              |
| /quiz/{クイズID}/edit           | quiz.edit                      |  GET   |![鍵][鍵]| クイズ編集開始ページを表示する。                                    |
| /quiz/{クイズID}/edit/confirm   | quiz.edit.conf                 |  POST   |![鍵][鍵]| クイズ編集確認ページを表示する。                                  |
| /quiz/{クイズID}/edit           | quiz.edit                      |  PATCH   |![鍵][鍵]| クイズ編集処理を実行する。                                      |
| /quiz/{クイズID}/answer         | quiz.answer                     |    GET   |      | クイズ回答開始ページを表示する。                                   |
| /quiz/{クイズID}/answer/confirm | quiz.answer.conf                |   POST   |      | クイズ回答確認ページを表示する。                                   |
| /quiz/{クイズID}/answer/result  | quiz.answer.result              |   POST   |      | クイズ回答結果ページを表示する。                                   |
| /quiz/{クイズID}/like           | quiz.like                       |    PUT   |![鍵][鍵]| クイズにいいね追加する。                                           |
| /quiz/{クイズID}/like           | quiz.like                       |  DELETE  |![鍵][鍵]| クイズのいいねを解除する。                                         |
| /quiz/{クイズID}/comments       | quiz.comment                   |   POST   |![鍵][鍵]| クイズにコメントを追加する。                                       |
| /quiz/{クイズID}/comments/{コメントID}| quiz.comment.edit         |   PATCH   |![鍵][鍵]| クイズのコメントを編集する。                                       |
| /quiz/{クイズID}/comments/{コメントID}| quiz.comment.edit         |   DELETE  |![鍵][鍵]| クイズのコメントを削除する。                                       |
| /grade/{ユーザID?}                | grade                           |    GET   |         | 成績ページを表示する。                                         |
| /grade/{ユーザID}/{グレードID}     | grade.detail                   |    GET   |         | 成績詳細ページを表示する。                                     |
| /user/show/grade                | show.grade                       |  PATCH | ![鍵][鍵]| 成績ページを他ユーザに表示するかどうかの設定をする。            |
| /register                       | register                        |    GET   |      | 会員登録ページを表示する。                                         |
| /register                       | register                        |   POST   |      | 会員登録処理を実行する。                                           |
| /login                          | login                           |    GET   |      | ログイン画面を表示する。                                           |
| /login                          | login                           |   POST   |      | ログイン処理を実行する。                                           |
| /logout                         | logout                          |   POST   |![鍵][鍵]| ログアウト処理を実行する。                                         |
| /forgot-password                | password.request                |    GET   |      | パスワードリセットメール送信用画面を表示する。                     |
| /forgot-password                | password.email                  |   POST   |      | パスワードリセットメール送信処理を実行する。                       |
| /reset-password/{token}         | password.reset                  |    GET   |      | パスワードリセット画面を表示する。                                 |
| /reset-password                 | password.update                 |   POST   |      | パスワードリセット処理を実行する。                                 |
| /user/profile                   | profile.show                    |    GET   |![鍵][鍵]| ユーザのプロファイル画面を表示する。                               |
| /user/profile-information       | user-profile-information.update |    PUT   |![鍵][鍵]| ユーザ情報（アイコン、名前、メールアドレス）の変更処理を実行する。 |
| /user/profile-photo             | current-user-photo.destroy      |  DELETE  |![鍵][鍵]| ユーザのアイコンの削除処理を実行する。                             |
| /user/password                  | user-password.update            |    PUT   |![鍵][鍵]| ユーザのパスワードの変更処理を実行する。                           |
| /user                           | current-user.destroy            |  DELETE  |![鍵][鍵]| ユーザを削除する。                                             |


<br><br>

## ER図
ER図は以下画像の通りです。
![ER図][ER図]
<br><br>

## テーブル定義
テーブル定義は以下表の通りです。
<br>

> ### usersテーブル
- クイズの作成、実行などをするユーザを管理します。

|     カラム論理名     |        カラム物理名       |          型         | PRIMARY | UNIQUE | NOT NULL | FOREIGN |
|:--------------------|:-------------------------|:-------------------:|:-------:|:------:|:--------:|:-------:|
| ユーザID             | id                        |        SERIAL       |![キー][キー]|![チェック][チェック]|![チェック][チェック]|         |
| ユーザ名             | name                      |     VARCHAR(255)    |         |        |![チェック][チェック]|         |
| メールアドレス       | email                     |     VARCHAR(255)    |         |![チェック][チェック]|![チェック][チェック]|         |
| メール確認日         | email_verified_at         |      TIMESTAMP      |         |        |          |         |
| パスワード           | password                  |     VARCHAR(255)    |         |        |![チェック][チェック]|         |
| リメンバートークン   | remember_token            |     VARCHAR(100)    |         |        |          |         |
| プロファイル画像パス | profile_photo_path        |         TEXT        |         |        |          |         |
| クイズ実行履歴表示フラグ | show_grade        |         SMALLINT        |         |        |![チェック][チェック]|         |
| 作成日               | created_at                |      TIMESTAMP      |         |        |          |         |
| 更新日               | updated_at                |      TIMESTAMP      |         |        |          |         |

<br>

> ### quizzesテーブル
- ユーザーが作成したクイズを管理します。

| カラム論理名   | カラム物理名 |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN  |
|----------------|--------------|:---------------:|:-------:|:------:|:--------:|:----------:|
| クイズID       | id           |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|            |
| ユーザID       | user_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]|  ![外部][外部]&nbsp;users(id) |
| ジャンルID     | genre_id     | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;genres(id) |
| クイズタイトル | title        |   VARCHAR(100)  |         |        |![チェック][チェック]|            |
| 説明文         | description  |   VARCHAR(255)  |         |        |          |            |
| 画像名         | filename     |   VARCHAR(255)  |         |        |          |            |
| 作成日         | created_at   |    TIMESTAMP    |         |        |          |            |
| 更新日         | updated_at   |    TIMESTAMP    |         |        |          |            |

<br>

> ### genresテーブル
- クイズのジャンルを管理します。

| カラム論理名 | カラム物理名 |      型     | PRIMARY | UNIQUE | NOT NULL | FOREIGN |
|--------------|--------------|:-----------:|:-------:|:------:|:--------:|:-------:|
| ジャンルID   | id           |    SERIAL   |![キー][キー]|![チェック][チェック]|![チェック][チェック]|         |
| ジャンル名   | name         | VARCHAR(20) |         |![チェック][チェック]|![チェック][チェック]|         |
| 作成日       | created_at   |  TIMESTAMP  |         |        |          |         |
| 更新日       | updated_at   |  TIMESTAMP  |         |        |          |         |

<br>

> ### itemsテーブル
- クイズの設問を管理します。

| カラム論理名     | カラム物理名    |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN   |
|------------------|-----------------|:---------------:|:-------:|:------:|:--------:|:-----------:|
| クイズアイテムID | id              |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|             |
| クイズID         | quiz_id         | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;quizzes(id) |
| 質問番号         | question_number |    SAMLLIINT    |         |        |![チェック][チェック]|             |
| 回答形式         | format          |     SMALLINT    |         |        |![チェック][チェック]|             |
| 質問             | question        |   VARCHAR(255)  |         |        |![チェック][チェック]|             |
| 答え             | answer          |   VARCHAR(255)  |         |        |![チェック][チェック]|             |
| 選択肢1          | choice1         |   VARCHAR(255)  |         |        |          |             |
| 選択肢2          | choice2         |   VARCHAR(255)  |         |        |          |             |
| 選択肢3          | choice3         |   VARCHAR(255)  |         |        |          |             |
| 選択肢4          | choice4         |   VARCHAR(255)  |         |        |          |             |
| 作成日           | created_at      |    TIMESTAMP    |         |        |          |             |
| 更新日           | updated_at      |    TIMESTAMP    |         |        |          |             |

<br>

> ### gradesテーブル
- クイズの実行履歴を管理します。

| カラム論理名 | カラム物理名  |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN   |
|--------------|---------------|:---------------:|:-------:|:------:|:--------:|:-----------:|
| 成績ID       | id            |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|             |
| クイズID     | quiz_id       | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;quizzes(id) |
| ユーザID     | user_id       | BIGINT UNSIGNED |         |        |          |  ![外部][外部]&nbsp;users(id)  |
| 作成日       | created_at    |    TIMESTAMP    |         |        |          |             |
| 更新日       | updated_at    |    TIMESTAMP    |         |        |          |             |

<br>

> ### answersテーブル
- クイズの実行履歴の回答内容などを管理します。

| カラム論理名 | カラム物理名  |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN   |
|--------------|---------------|:---------------:|:-------:|:------:|:--------:|:-----------:|
| 成績回答ID    | id            |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|             |
| 成績ID        | grade_id       | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;grades(id) |
| クイズアイテムID| item_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;items(id) |
| 回答実績       | answer       |   VARCHAR(255)  |         |        |![チェック][チェック]|             |
| 正解有無       | pass       |  BOOLEAN         |         |        |![チェック][チェック]|             |
| 作成日       | created_at    |    TIMESTAMP    |         |        |          |             |
| 更新日       | updated_at    |    TIMESTAMP    |         |        |          |             |

<br>

> ### commentsテーブル
- クイズに対するコメントを管理します。

| カラム論理名 | カラム物理名 |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN   |
|--------------|--------------|:---------------:|:-------:|:------:|:--------:|:-----------:|
| コメントID   | id           |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|             |
| クイズID     | quiz_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;quizzes(id) |
| ユーザID     | user_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]|  ![外部][外部]&nbsp;users(id)  |
| 内容         | content      |   VARCHAR(255)  |         |        |![チェック][チェック]|             |
| 作成日       | created_at   |    TIMESTAMP    |         |        |          |             |
| 更新日       | updated_at   |    TIMESTAMP    |         |        |          |             |

<br>

> ### likesテーブル
- クイズに対するいいねを管理します。

| カラム論理名 | カラム物理名 |        型       | PRIMARY | UNIQUE | NOT NULL |   FOREIGN   |
|--------------|--------------|:---------------:|:-------:|:------:|:--------:|:-----------:|
| いいねID     | id           |      SERIAL     |![キー][キー]|![チェック][チェック]|![チェック][チェック]|             |
| クイズID     | quiz_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]| ![外部][外部]&nbsp;quizzes(id) |
| ユーザID     | user_id      | BIGINT UNSIGNED |         |        |![チェック][チェック]|  ![外部][外部]&nbsp;users(id)  |
| 作成日       | created_at   |    TIMESTAMP    |         |        |          |             |
| 更新日       | updated_at   |    TIMESTAMP    |         |        |          |             |

<br>
