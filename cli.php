<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Arguments;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\CreateUserCommand;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException|Exception $e) {
    echo "{$e->getMessage()}\n";
}
