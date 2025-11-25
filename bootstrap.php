<?php

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

// Создаём объект контейнера ..
$container = new DIContainer();

// Добавляем правило для подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
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

// Добавляем логгер в контейнер
$container->bind(
    // С контрактом логгера из PSR-3
    LoggerInterface::class,
    // Ассоциируем объект логгера из библиотеки monolog
    // blog – это (произвольное) имя логгера
    // Логгер вызывает обработчики один за другим в направлении от последнего к первому
    (new Logger('blog'))
        // Настраиваем логгер
        ->pushHandler(new StreamHandler(
            // записывать в файл '/logs/blog.log'
            __DIR__ . '/logs/blog.log' // Путь до этого файла
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
        ))
        // Настраиваем логгер
        ->pushHandler(
            // вести запись в поток "php://stdout", то есть в консоль
            new StreamHandler("php://stdout")
        )
    ,
);

// Возвращаем объект контейнера
return $container;
