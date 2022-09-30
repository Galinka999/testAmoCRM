## App integration with AmoCRM

Приложение для выгрузки сделок и других сущностей из amoCRM.

Технологии: PHP 8.1, Laravel. База данных - PostgreSQL.

## Install
- git clone ...
- cd testAmoCRM
- create a file .env(настроить подключение к БД, прописать настройки AmoCRM)
- php artisan key:gen
- docker compose build
- docker compose up -d
- docker exec -it amocrm-app-laravel.test-1  sh (зайти в контейнер)
- php artisan migrate
