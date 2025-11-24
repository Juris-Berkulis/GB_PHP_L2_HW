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

// Возвращаем объект контейнера
return $container;
