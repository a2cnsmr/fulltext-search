全文検索
===

# はじめに

MySQLの全文検索をやってみます。

# データを用意する

利便性が良くなった郵便番号データを使用します。  
[住所の郵便番号（1レコード1行、UTF-8形式）（CSV形式）](https://www.post.japanpost.jp/zipcode/dl/utf-zip.html)  

ダウンロードしたら ZIP ファイルを解凍し、生成される `utf_all.csv` を storage/logs に設置します。

### 1. docker-compose.ymlを編集する

`mysql` ディレクティブの `volume` に、Laravel の `storage` を追加し、ファイルを直接インポートできるようにします。

```yaml
volumes:
    - 'sail-mysql:/var/lib/mysql'
    - './vendor/laravel/sail/database/mysql/create-testing-database.sh:/docker-entrypoint-initdb.d/10-create-testing-database.sh'
    - './storage:/storage' # ←追加
```

### 2. マイグレーションを実施

```bash
./vendor/bin/sail artisan migrate:fresh
```

### 3. MySQL コマンドでインポートする

Laravel sail には `sail mysql` というコマンドがありますが、ここから利用できる MySQL コンソールでインポートをするとエラーが発生します。  

```sql
ERROR 3948 (42000): Loading local data is disabled; this must be enabled on both the client and server sides
```

ファイルからの入力は無効にされているようです。  

```bash
# 直接コンテナへ入る
docker exec -it fulltext-search-mysql-1 sh

# コンテナに入ったあと、mysqlコンソールを実行
mysql -u root -p --local_infile=1
```

```mysql
-- DB切り替え
use fulltext_search

-- 永続的に設定を変更
SET PERSIST local_infile= 1;

-- インポート
load data local infile '/storage/logs/utf_all.csv'
    into table postal_code_tmp 
    fields terminated by ',' 
    optionally enclosed by '"' 
    lines terminated by '\n';
```

実行結果

```mysql
mysql> load data local infile '/storage/logs/utf_all.csv' into table postal_code_tmp fields terminated by ',' optionally enclosed by '"' lines terminated by '\n';
Query OK, 124319 rows affected, 40 warnings (0.58 sec)
Records: 124319  Deleted: 0  Skipped: 0  Warnings: 40

mysql> select count(*) from postal_code_tmp;
+----------+
| count(*) |
+----------+
|   124319 |
+----------+
1 row in set (0.03 sec)
```
