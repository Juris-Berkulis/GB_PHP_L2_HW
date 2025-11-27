# GB PHP L2 HW

# Документация по Composer скриптам

## Доступные скрипты

### 🧪 `test`
**Команда:** `composer test`  
**Назначение:** Запуск базового набора тестов  
**Вывод:** Цветной вывод в консоли с названиями тестов  
**Флаги:**
- `--testdox` - Читаемые названия тестов
- `--colors=always` - Цветной вывод для лучшей читаемости

### 📊 `test-mode-coverage`
**Команда:** `composer test-mode-coverage`  
**Назначение:** Запуск тестов с выводом сводки покрытия в консоль  
**Вывод:** Текстовая сводка в терминале  
**Требования:** Расширение Xdebug с режимом coverage  
**Использование:** Быстрая проверка покрытия во время разработки

### 🌐 `test-mode-coverage-html`
**Команда:** `composer test-mode-coverage-html`  
**Назначение:** Генерация детального HTML отчёта о покрытии  
**Вывод:** Интерактивные HTML файлы в папке `coverage_report/`  
**Требования:** Расширение Xdebug с режимом coverage  
**Использование:** Детальный анализ в браузере

### 📋 `test-mode-coverage-clover`
**Команда:** `composer test-mode-coverage-clover`  
**Назначение:** Генерация XML отчёта для CI/CD систем  
**Вывод:** Файл `coverage_report.xml` в формате Clover  
**Требования:** Расширение Xdebug с режимом coverage  
**Использование:** Интеграция с Jenkins, GitLab CI и другими системами

## Примеры использования

### Рабочий процесс разработки
```bash
# Быстрый запуск тестов
composer test

# Проверка процента покрытия
composer test-mode-coverage

# Генерация детального HTML отчёта
composer test-mode-coverage-html
# Затем открыть: coverage_report/index.html

# Генерация XML для CI систем
composer test-mode-coverage-clover
```

# Документация по API и отладке

## Запуск сервера

Для запуска локального сервера разработки выполните команду:

```bash
php -S localhost:80 http_api.php
```

Сервер будет доступен по адресу: `http://localhost:80`

## API Эндпоинты

**Открытие окна отправления api-запросов:**

`Tools → HTTP Client → Create Request in HTTP Client`

### Аутентификация

**Метод:** POST

**URL:** `http://localhost:80/login`

**Заголовки:**
```http
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "username": "user",
  "password": "pass"
}
```

**Параметры:**

`username` (string) - username пользователя

`password` (string) - пароль пользователя

**Пример:**

```http
POST http://localhost:80/login
Content-Type: application/json

{
    "username": "user4",
    "password": "pass1"
}
```

### Получение пользователя по username

**Метод:** GET

**URL:** `http://localhost:80/users/show?username={username}`

**Пример:**
```http
GET http://localhost:80/users/show?username=ivan
```

### Создание статьи

**Метод:** POST

**URL:** `http://localhost:80/posts/create`

**Заголовки:**
```http
Content-Type: application/json

Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3
```

**Тело запроса:**
```json
{
    "text": "some text",
    "title": "some title"
}
```

**Параметры:**

`text` (string) - Текст статьи

`title` (string) - Заголовок статьи

**Пример:**

```http
POST http://localhost:80/posts/create
Content-Type: application/json
Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3

{
    "text": "some text",
    "title": "some title"
}
```

### Удаление статьи

**Метод:** DELETE

**URL:** `http://localhost:80/posts?uuid={uuid}`

**Пример:**
```http
DELETE http://localhost:80/posts?uuid=c9ccaec2-88b4-41cd-b092-9d64ce9d478a
```

### Создание комментария

**Метод:** POST

**URL:** `http://localhost:80/posts/comment`

**Заголовки:**
```http
Content-Type: application/json

Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3
```

**Тело запроса:**
```json
{
    "post_uuid": "6a248a05-d352-4c0b-96e9-4a205a61a6a9",
    "text": "some text"
}
```

**Параметры:**

`post_uuid` (string) - UUID статьи

`text` (string) - Текст комментария

**Пример:**

```http
POST http://localhost:80/posts/comment
Content-Type: application/json
Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3

{
    "post_uuid": "6a248a05-d352-4c0b-96e9-4a205a61a6a9",
    "text": "some text"
}
```

### Добавление лайка статье

**Метод:** POST

**URL:** `http://localhost:80/like/post`

**Заголовки:**
```http
Content-Type: application/json

Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3
```

**Тело запроса:**
```json
{
    "post_uuid": "6a248a05-d352-4c0b-96e9-4a205a61a6a9"
}
```

**Параметры:**

`post_uuid` (string) - UUID статьи, которой добавляется лайк

**Пример:**

```http
POST http://localhost:80/like/post
Content-Type: application/json
Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3

{
    "post_uuid": "6a248a05-d352-4c0b-96e9-4a205a61a6a9"
}
```

### Добавление лайка комментарию

**Метод:** POST

**URL:** `http://localhost:80/like/comment`

**Заголовки:**
```http
Content-Type: application/json

Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3
```

**Тело запроса:**
```json
{
    "comment_uuid": "eefb7270-1c8c-49d5-9c69-421584ee61ca"
}
```

**Параметры:**

`comment_uuid` (string) - UUID комментария, которому добавляется лайк

**Пример:**

```http
POST http://localhost:80/like/comment
Content-Type: application/json
Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3

{
    "comment_uuid": "51088f54-4f61-439e-bab5-5815ab62b728"
}
```

## Отладка с Xdebug

Для включения отладки добавьте заголовок в запрос:

```http
Cookie: XDEBUG_SESSION=start
```

Пример полного запроса с отладкой:
```http
POST http://localhost:80/posts/create
Content-Type: application/json
Authorization: Bearer 0f36a75f28e2811b4a5d8d15f8ac1b61f7e4edbe4b4135cf6f71fa784d4d4ee3b6a8bb4c51fca4d3
Cookie: XDEBUG_SESSION=start

{
    "text": "some text",
    "title": "some title"
}
```
