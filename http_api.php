<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Users\FindByUsername;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

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

$routes = [
    'GET' => [
        // Действие, соответствующее пути /users/show
        '/users/show' => new FindByUsername(
            // Действию нужен репозиторий
            new SqliteUsersRepository(
                // Репозиторию нужно подключение к БД
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
//        // Действие, соответствующее пути /posts/show
//        '/posts/show' => new FindByUuid(
//            new SqlitePostsRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//            )
//        ),
    ],
    'POST' => [
//        // Действие, соответствующее пути /posts/create
//        '/posts/create' => new CreatePost(
//            new SqlitePostsRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//            ),
//            new SqliteUsersRepository(
//                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//            )
//        ),
    ],
];

// Если нет маршрутов для метода запроса - возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Зопрос содержит неизвестный метод'))->send();

    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Запрос содержит неизвестный URI'))->send();

    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    // Пытаемся выполнить действие, при этом результатом может быть как успешный, так и неуспешный ответ
    $response = $action->handle($request);

    // Отправляем ответ
    $response->send();
} catch (AppException $e) {
    // Отправляем неудачный ответ, если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
