<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Auth\LogIn;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Comments\CreateComment;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfComments\CreateLikeOfComment;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfPosts\CreateLikeOfPost;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts\CreatePost;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts\DeletePost;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Users\FindByUsername;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    // Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());

    // Отправляем неудачный ответ, если по какой-то причине не можем получить путь
    (new ErrorResponse($e->getMessage()))->send();

    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());

    // Возвращаем неудачный ответ, если по какой-то причине не можем получить метод
    (new ErrorResponse($e->getMessage()))->send();

    return;
}

/**
 * Имена классов действий по пути из URL
 *
 * При добавлении маршрута необходимо
 * добавить правила в файле "bootstrap.php"
 * для соответствующиго класса и его зависимостей
 */
$routes = [
    'GET' => [
        // Отображение пользователя по его username
        '/users/show' => FindByUsername::class,
//        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        // Обмен пароля на токен
        '/login' => LogIn::class,
        // Создание статьи
        '/posts/create' => CreatePost::class,
        // Создание комментария
        '/posts/comment' => CreateComment::class,
        // Добавление лайка к статье
        '/like/post' => CreateLikeOfPost::class,
        // Добавление лайка к комментарию
        '/like/comment' => CreateLikeOfComment::class,
    ],
    'DELETE' => [
        // Удаление статьи
        '/posts' => DeletePost::class,
    ],
];

// Если нет маршрутов для метода запроса - возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    // Логируем сообщение с уровнем NOTICE
    $logger->notice("Зопрос содержит неизвестный метод: $method");

    (new ErrorResponse("Зопрос содержит неизвестный метод: $method"))->send();

    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $logger->notice("Зопрос содержит неизвестный путь: $path");

    (new ErrorResponse("Запрос содержит неизвестный путь: $path"))->send();

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
    switch(true) {
        case $e instanceof AppException: {
            // Логируем сообщение с уровнем WARNING
            $logger->warning($e->getMessage());

            break;
        }

        default: {
            // Логируем сообщение с уровнем ERROR
            $logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    // Отправляем неудачный ответ, если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
