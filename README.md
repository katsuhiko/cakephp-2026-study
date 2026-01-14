# CakePHP 2026 Study

```
docker compose up -d
docker exec -it cli bash
```

## CMSチュートリアル

```
docker exec -it cli bash
php composer.phar create-project cakephp/app:5.* cms
cd ./cms
```

### ライブラリインストール

```
php ../composer.phar require --dev phpstan/phpstan

php ../composer.phar require cakephp/migrations "@stable"
bin/cake plugin load Migrations
```

### Bake

```
bin/cake bake migration CreateUsers email:string password:string created modified
bin/cake bake migration CreateArticles user_id:integer title:string slug:string[191]:unique body:text published:boolean created modified
bin/cake bake migration CreateTags title:string[191]:unique created modified
bin/cake bake migration CreateArticlesTags article_id:integer:primary tag_id:integer:primary created modified

bin/cake bake seed Users
bin/cake bake seed Articles

bin/cake bake model Users
bin/cake bake model Articles
bin/cake bake model Tags
bin/cake bake model ArticlesTags
```

### マイグレーション

```
bin/cake migrations migrate
bin/cake migrations seed --seed UsersSeed --seed ArticlesSeed

docker exec -it -w /app/cms cli bin/cake migrations migrate
docker exec -it -w /app/cms cli bin/cake migrations seed --seed UsersSeed --seed ArticlesSeed
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
sqlite3 ./tmp/products.sqlite
sqlite> .mode column
sqlite> .headers on

docker exec -it -w /app/cms cli sqlite3 ./tmp/products.sqlite
sqlite> .mode column
sqlite> .headers on
```
