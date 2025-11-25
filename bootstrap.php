<?php

use Dotenv\Dotenv;
use JurisBerkulis\GbPhpL2Hw\Blog\Container\DIContainer;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository\LikesOfCommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository\SqliteLikesOfCommentsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository\LikesOfPostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository\SqliteLikesOfPostsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();

// Добавляем правило для подключение к БД
$container->bind(
    PDO::class,
    // Берём путь до файла базы данных SQLite из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);

// Добавляем правило для репозитория статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

// Добавляем правило для репозитория пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    LikesOfPostsRepositoryInterface::class,
    SqliteLikesOfPostsRepository::class
);

$container->bind(
    LikesOfCommentsRepositoryInterface::class,
    SqliteLikesOfCommentsRepository::class,
);

/**
 * Объект логгера из библиотеки monolog
 *
 * blog – это (произвольное) имя логгера
 *
 * Логгер вызывает обработчики один за другим в направлении от последнего к первому
 */
$logger = (new Logger('blog'));

// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
         // Настраиваем логгер
        ->pushHandler(new StreamHandler(
            // записывать в файл '/logs/blog.log'
            __DIR__ . '/logs/blog.log'
        ))
        // Настраиваем логгер
        ->pushHandler(new StreamHandler(
            // записывать в файл "blog.error.log"
            __DIR__ . '/logs/blog.error.log',
            // события с уровнем ERROR и выше,
            level: Logger::ERROR,
            // при этом событие не должно "всплывать" (т.е., если событие обработано,
            // оно не должно передаваться следующим обработчикам)
            bubble: false,
        ));
}

// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        // Настраиваем логгер
        ->pushHandler(
            // вести запись в поток "php://stdout", то есть в консоль
            new StreamHandler("php://stdout")
        );
}

// Добавляем логгер в контейнер
$container->bind(
    // Контракт логгера из PSR-3
    LoggerInterface::class,
    $logger,
);

// Возвращаем объект контейнера
return $container;
