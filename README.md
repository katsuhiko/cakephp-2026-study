# CakePHP 2026 Study

```
docker compose up -d
docker exec -it cli bash
```

## スケルトン作成

```
php composer.phar create-project cakephp/app:5 cms
```

## CMSチュートリアル

```
docker exec -it cli bash
cd ./cms
```

### ライブラリインストール

```
php ../composer.phar require --dev phpstan/phpstan

docker exec -it -w /app/cms cli php ../composer.phar require --dev phpstan/phpstan
```

### Push 前確認

```
php ../composer.phar check

docker exec -it -w /app/cms cli php ../composer.phar check
```

### サーバー立ち上げ

```
bin/cake server -H 0.0.0.0

docker exec -it -w /app/cms cli bin/cake server -H 0.0.0.0
```

### Sqlite

```
sqlite3 ./tmp/products.sqlite < ./config/schema/cms.sql

```
sqlite3 ./tmp/products.sqlite
sqlite> .mode column
sqlite> .headers on

docker exec -it -w /app/cms cli sqlite3 ./tmp/products.sqlite
sqlite> .mode column
sqlite> .headers on
```
