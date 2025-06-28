<?php

use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommandsRepository\SqliteCommandsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

require_once __DIR__ . '/vendor/autoload.php';

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$faker = Faker\Factory::create('ru_RU');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

//$command = new CreateUserCommand($usersRepository);

//// Создаём пользователя с помощью терминальной команды
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (Exception $e) {
//    echo 'Ошибка: ', $e->getMessage();
//}

$postsRepository = new SqlitePostsRepository($connection, $usersRepository);

//// Создаём статью
//try {
//    $postsRepository->save(new Post(
//        UUID::random(),
//        $usersRepository->getByUsername('admin'),
//        $faker->realText(15),
//        $faker->realText(50),
//    ));
//} catch (Exception $e) {
//    echo $e->getMessage();
//}

//// Получение статьи по её uuid
//try {
//    echo $postsRepository->get(new UUID('4d3f394d-581a-443b-a639-659cf28b8e17'));
//} catch (Exception $e) {
//    echo $e->getMessage();
//}

$commandsRepository = new SqliteCommandsRepository(
    $connection,
    $postsRepository,
    $usersRepository,
);

//// Создаём Комментарий
//try {
//    $commandsRepository->save(new Comment(
//        UUID::random(),
//        $usersRepository->getByUsername('admin'),
//        $postsRepository->get(new UUID('2ddce9b1-5ae9-45dd-95cf-b5c5564de4ed')),
//        $faker->realText(30),
//    ));
//} catch (Exception $e) {
//    echo $e->getMessage();
//}

// Получение комментарий по его uuid
try {
    echo $commandsRepository->get(new UUID('884a5019-041d-42c1-9739-8255a2538978'));
} catch (Exception $e) {
    echo $e->getMessage();
}
