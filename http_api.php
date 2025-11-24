<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Comments\CreateComment;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts\CreatePost;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts\DeletePost;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Users\FindByUsername;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    // Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ, если по какой-то причине не можем получить путь
    (new ErrorResponse)->send();

    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ, если по какой-то причине не можем получить метод
    (new ErrorResponse)->send();

    return;
}

// Ассоциируем маршруты с именами классов действий
$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
//        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];

// Если нет маршрутов для метода запроса - возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Зопрос содержит неизвестный метод: $method"))->send();

    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Запрос содержит неизвестный URI: $path"))->send();

    return;
}

// Получаем имя класса действия для маршрута по методу и пути
$actionClassName = $routes[$method][$path];

// С помощью контейнера создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    // Пытаемся выполнить действие, при этом результатом может быть как успешный, так и неуспешный ответ
    $response = $action->handle($request);

    // Отправляем ответ
    $response->send();
} catch (AppException|Exception $e) {
    // Отправляем неудачный ответ, если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
