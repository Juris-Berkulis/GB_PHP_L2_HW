<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Users\FindByUsername;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

// Объект запроса из суперглобальных переменных
$request = new Request($_GET, $_SERVER);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ, если по какой-то причине не можем получить путь
    (new ErrorResponse)->send();

    // Выходим из программы
    return;
}

$routes = [
    // Действие, соответствующее пути /users/show
    '/users/show' => new FindByUsername(
        // Действию нужен репозиторий
        new SqliteUsersRepository(
            // Репозиторию нужно подключение к БД
            new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
        )
    ),
//    // Второй маршрут
//    '/posts/show' => new FindByUuid(
//        new SqlitePostsRepository(
//            new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
//        )
//    ),
];

// Если нет маршрута для пути из запроса, отправляем неуспешный ответ
if (!array_key_exists($path, $routes)) {
    (new ErrorResponse('Not found'))->send();

    return;
}

// Выбираем найденное действие
$action = $routes[$path];

try {
    // Пытаемся выполнить действие, при этом результатом может быть как успешный, так и неуспешный ответ
    $response = $action->handle($request);

    // Отправляем ответ
    $response->send();
} catch (AppException $e) {
    // Отправляем неудачный ответ, если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
