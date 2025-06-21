<?php

// Использование: Запустить в терминале команды
// - php cli.php user
// - php cli.php post
// - php cli.php comment
// - php cli.php test (или любой другой параметр, либо без параметра)

use JurisBerkulis\GbPhpL2Hw\Person\Name;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Comment;

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

$faker = Faker\Factory::create('ru_RU');

$name = new Name($faker->firstName('male'), $faker->lastName('male'));
$user = new User($faker->randomDigitNotNull(), $name, $faker->email());

$post = new Post(
    $faker->randomDigitNotNull(),
    $user,
    $faker->text(15),
    $faker->text(150),
);

$route = $argv[1] ?? null;

switch ($route) {
    case "user": {
        echo $user;

        break;
    }
    case "post": {
        echo $post;

        break;
    }
    case "comment": {
        $comment = new Comment(
            $faker->randomDigitNotNull(),
            $user,
            $post,
            $faker->text(50),
        );

        echo $comment;

        break;
    }
    default:
        echo "Неизвестная комманда!\n";
}
