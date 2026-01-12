# CakePHP 2026 Study

```
php composer.phar create-project cakephp/app:5 cms
```

```
cd ./cms
bin/cake server -H 0.0.0.0
```

```
cd ./cms
sqlite3 ./tmp/products.sqlite < ./config/schema/cms.sql

sqlite3 ./tmp/products.sqlite
sqlite> .mode column
sqlite> .headers on
```
