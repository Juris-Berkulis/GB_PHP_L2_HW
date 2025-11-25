<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Arguments;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\CreateUserCommand;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException|Exception $e) {
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);

    echo "{$e->getMessage()}\n";
}
