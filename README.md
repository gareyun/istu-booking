# istu-booking
Сервис бронирования аудиторий для университета

Инструкция по запуску:
1) Склонировать репозиторий
2) Выполнить composer install
3) cp .env.example .env (для тестировочного запуска)
4) php artisan key:generate
5) php artisan migrate --seed (прописана очерёдность сидеров в DatabaseSeeder.php)
6) npm install
7) Запуск сервиса через npm run dev и php artisan serve
