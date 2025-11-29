<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Posts\DeletePost;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Users\CreateUser;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    // Добавить пользователя
    CreateUser::class,
    // Удалить статью
    DeletePost::class,
    // Изменить (имя и фамилию) пользователя
    UpdateUser::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера создаём объект команды
    $command = $container->get($commandClass);

    // Добавляем команду к приложению
    $application->add($command);
}

// Запускаем приложение
try {
    $application->run();
} catch (Exception $e) {
    echo "{$e->getMessage()}\n";
}
