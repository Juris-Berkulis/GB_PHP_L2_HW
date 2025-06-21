<?php

use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Person\{Name, Person};
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\InMemoryUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;

include __DIR__ . "/vendor/autoload.php";

///**
// * Кастомная загрузка файлов
// * @param $className - Название файла (без расширения)
// * @return void
// */
//function load($className)
//{
//    $file = $className . ".php";
//    $file = str_replace("JurisBerkulis\\GbPhpL2Hw", "src", $file);
//    $file = str_replace("_", "/", $file);
//    $file = str_replace("\\", "/", $file);
//
//    if (file_exists($file)) {
//        include $file;
//    }
//}
//
//spl_autoload_register('load');

var_dump($argv);

//$faker = Faker\Factory::create('ru_RU');

//echo $faker->name() . PHP_EOL;
//echo $faker->realText(rand(100, 200)) . PHP_EOL;

$name = new Name('Peter', 'Sidorov');
$user = new User(1, $name, "Admin");

$person = new Person($name, new DateTimeImmutable());

$post = new Post(
    1,
    $person,
    'Всем привет!'
);

echo $post;

$name2 = new Name('Иван', 'Таранов');
$user2 = new User(2, $name2, "User");

$userRepository = new InMemoryUsersRepository();

try {
    $userRepository->save($user);
    $userRepository->save($user2);

    echo $userRepository->get(1);
    echo $userRepository->get(2);
    echo $userRepository->get(3);
} catch (UserNotFoundException | Exception $e) {
    echo $e->getMessage();
}