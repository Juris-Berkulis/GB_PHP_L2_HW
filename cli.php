<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Arguments;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\CreateUserCommand;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo 'Ошибка: ', $e->getMessage();
}
