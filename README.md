# Личный проект «What to watch»

* Студент: [Борис Глинский](https://htmlacademy.ru/profile/colspan).
* Наставник: [Сергей Парфенов](https://htmlacademy.ru/profile/id926645).

---

# Установка приложения

* Создайте свой .env файл на основе .env.example
* Выполните команды:
- `composer install` - установка необходимых зависимостей;
- `php artisan key:generate` - генерация ключа приложения;
- `./vendor/bin/sail up -d` - запуск докер контейнеров;
- `./vendor/bin/sail artisan migrate` - выполнение миграций;
- `./vendor/bin/sail artisan db:seed` - заполнение бд случайными данными;

# Запуск автотестов:
* `./vendor/bin/sail artisan test`
