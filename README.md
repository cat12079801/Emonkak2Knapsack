# Emonkak2Knapsack

プロジェクトで利用しているCollectionライブラリを[emonkak/php-collection](https://github.com/emonkak/php-collection)から[DusanKasan/Knapsack](https://github.com/DusanKasan/Knapsack)へ置き換える際に機械的に実行できるように作成したツール  
なお、PHP7.0.xを想定


## How to use

1. 対象リポジトリのルートディレクトリにclone
1. `composer install`

```
# 必要に応じて対象ファイルを変更
$ vim lib/TargetFiles.php

# 変換を行う
$ php convert.php

# 変換のドライランを行う
$ php convert.php --dry-run

# メソッドチェーンのメソッドと取る引数の一覧をjsonで出力する
$ php analyze.php

# テスト実行
$ vendor/bin/phpunit tests/
```


## テスト

コードの変換による動作を保証するためテストを書いています。以下のようにして変換後のコードの動作を保証します

```
$ vendor/bin/phpunit tests/
OK (X tests, X assertions)

$ php convert.php # コード変換

$ vendor/bin/phpunit tests/
OK (X tests, X assertions)
```

テストケースはEmonkakの動作を網羅しているのではなく、connect既存実装で使われているコードの動作を保証するように書いています  
既存実装で利用しているメソッド、引数の数・型は `analyze.php` で確認
