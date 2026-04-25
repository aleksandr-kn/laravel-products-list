# Каталог товаров

Веб-приложение для управления каталогом товаров с административной панелью

## Стек

- **Backend:** Laravel 11, PHP 8.3
- **Frontend:** Vue 3 (Composition API), InertiaJS, Tailwind CSS
- **БД:** PostgreSQL 15
- **Аутентификация:** Laravel Sanctum (токен)
- **Инфраструктура:** Docker, Nginx

## Требования

- Docker и Docker Compose
- Node.js 18+ (для сборки фронтенда)

## Установка и запуск

### 1. Клонировать репозиторий

```bash
git clone <url> catalog
cd catalog
```

### 2. Настроить окружение

```bash
cp .env.example .env
```

### 3. Запустить контейнеры

```bash
docker compose up -d
```

Будут подняты: PHP-FPM, Nginx (порт 80), PostgreSQL (порт 5432).

### 4. Установить PHP-зависимости

```bash
docker compose exec app composer install
```

### 5. Сгенерировать ключ приложения

```bash
docker compose exec app php artisan key:generate
```

### 6. Запустить миграции и сидеры

```bash
docker compose exec app php artisan migrate --seed
```

Будут созданы таблицы и тестовые данные: 6 категорий, 40 товаров, admin-пользователь.

### 7. Собрать фронтенд

```bash
npm install
npm run build
```

### 8. Открыть приложение

Приложение доступно по адресу: http://localhost

## Тестовый пользователь

| Email | Пароль |
|---|---|
| admin@example.com | password |

## API

### Публичные эндпоинты

| Метод | URL | Описание |
|---|---|---|
| POST | /api/login | Авторизация, возвращает токен |
| GET | /api/products | Список товаров (пагинация, фильтр по category_id, поиск по search) |
| GET | /api/products/{id} | Один товар |
| GET | /api/categories | Все категории |

### Защищенные эндпоинты (требуется токен)

Передавать заголовок: `Authorization: Bearer <token>`

| Метод | URL | Описание |
|---|---|---|
| POST | /api/products | Создать товар |
| PUT | /api/products/{id} | Обновить товар |
| PATCH | /api/products/{id} | Частично обновить товар |
| DELETE | /api/products/{id} | Удалить товар (мягкое удаление) |

## Тесты

```bash
docker compose exec app php artisan test
```

Тесты используют SQLite in-memory, основная БД не затрагивается.

## Структура проекта

```
app/
  Http/
    Controllers/
      Api/                    - API-контроллеры (JSON)
      Admin/                  - Inertia-контроллеры админки
      PublicController.php    - Inertia-контроллер публичных страниц
    Requests/                 - валидация входящих данных
    Resources/                - трансформация ответов API
  Models/                     - Eloquent-модели
resources/js/
  Pages/                      - Vue-страницы (Inertia)
  Components/                 - переиспользуемые компоненты
  Composables/                - логика работы с API и авторизацией
docker/
  php/Dockerfile
  nginx/default.conf
```
