<!--- 画像URLの変数定義 --->
[画面遷移図]: https://user-images.githubusercontent.com/81066421/119996914-24878800-c00a-11eb-900d-6236d78f2dcd.png
[鍵]: https://user-images.githubusercontent.com/81066421/120003596-dd50c580-c010-11eb-9442-5542a6466dbb.png
[ER図]: https://user-images.githubusercontent.com/81066421/120052090-7e19a200-c05e-11eb-8850-5085b1ddde2b.png
[チェック]: https://user-images.githubusercontent.com/81066421/120052611-131d9a80-c061-11eb-9e86-f323d6cb2b41.png
[外部]: https://user-images.githubusercontent.com/81066421/120052614-13b63100-c061-11eb-8b16-a679f6241f26.png
[キー]: https://user-images.githubusercontent.com/81066421/120052616-144ec780-c061-11eb-9efc-0ab2224081ab.png


<!--- 本文はこちらから --->
# [QuizMaker](https://quiz-maker-777.herokuapp.com/)

## アプリ概要
クイズを作成・実行できるWEBアプリケーションです。
<br><br>

## 使用した技術
以下を使用してWEBアプリケーションを作成しました。
- **[Laravel](https://laravel.com/)**
- **[Laravel Sail](https://laravel.com/docs/8.x/sail/)**
- **[Laravel Jetstream](https://jetstream.laravel.com/2.x/)**
- **[Vue.js](https://jp.vuejs.org/)**
- **[Vuetify](https://vuetifyjs.com/ja/)**
- **[Inertia.js](https://inertiajs.com/)**
- **[axios](https://axios-http.com/)**
<br><br>

## 画面遷移図
画面遷移図は以下画像の通りです。
![画面遷移図][画面遷移図]
<br><br>

## URL一覧
URLの一覧は以下表の通りです。
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
| /user                           | current-user.destroy            |  DELETE  |![鍵][鍵]| ユーザを削除する。                                                 |
| /grade/{ユーザID}                | grade                           |    GET   |         | 成績ページを表示する。                                         |


<br><br>

## ER図
ER図は以下画像の通りです。
![ER図][ER図]
<br><br>

## テーブル定義
テーブル定義は以下表の通りです。
<br>

> ### usersテーブル
- クイズを作成、実行などをするユーザを管理します。

|     カラム論理名     |        カラム物理名       |          型         | PRIMARY | UNIQUE | NOT NULL | FOREIGN |
|:--------------------|:-------------------------|:-------------------:|:-------:|:------:|:--------:|:-------:|
| ユーザID             | id                        |        SERIAL       |![キー][キー]|![チェック][チェック]|![チェック][チェック]|         |
| ユーザ名             | name                      |     VARCHAR(255)    |         |        |![チェック][チェック]|         |
| メールアドレス       | email                     |     VARCHAR(255)    |         |![チェック][チェック]|![チェック][チェック]|         |
| メール確認日         | email_verified_at         |      TIMESTAMP      |         |        |          |         |
| パスワード           | password                  |     VARCHAR(255)    |         |        |![チェック][チェック]|         |
| リメンバートークン   | remember_token            |     VARCHAR(100)    |         |        |          |         |
| プロファイル画像パス | profile_photo_path        |         TEXT        |         |        |          |         |
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
| 正答数       | correct_count |   VARCHAR(100)  |         |        |![チェック][チェック]|             |
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
| 更新日       | updated_at   |    TIMESTAMP    |         |        |          |             |

<br>
