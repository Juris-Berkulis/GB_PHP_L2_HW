<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;

require_once __DIR__ . '/vendor/autoload.php';

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

try {
//    //Добавляем в репозиторий несколько пользователей
//    $usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), 'admin'));

//    //Извлекаем пользователя по uuid
//    echo $usersRepository->get(new UUID('67e8fc70-b1da-44d6-a61e-edbe8e24155d'));

    //Извлекаем пользователя по логину
    echo $usersRepository->getByUsername('admin');
} catch (InvalidArgumentException $e) {
    echo 'Ошибка типа InvalidArgumentException: ', $e->getMessage();
} catch (UserNotFoundException $e) {
    echo 'Ошибка типа UserNotFoundException: ', $e->getMessage();
} catch (Exception $e) {
    echo 'Ошибка типа Exception: ', $e->getMessage();
}
