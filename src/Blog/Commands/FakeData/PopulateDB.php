<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands\FakeData;

use Faker\Generator;
use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Создавать фейковых пользователей,
 * и от имени каждого вновь созданного пользователя — фейковые статьи
 *
 * Имена пользователей, заголовки и тексты статей
 * будут сгенерированы библиотекой Faker
 */
class PopulateDB extends Command
{

    public function __construct(
        // Внедряем генератор тестовых данных и репозитории пользователей, статей и комментариев
        private readonly Generator                   $faker,
        private readonly UsersRepositoryInterface    $usersRepository,
        private readonly PostsRepositoryInterface    $postsRepository,
        private readonly CommentsRepositoryInterface $commentsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Заполняет базу данных фейковыми данными')
            ->addOption(
                // Имя опции
                'users-number',
                // Сокращённое имя
                'u',
                // Значение этой опции является обязательным (сама опция по-прежнему необязательна)
                InputOption::VALUE_REQUIRED,
                // Описание
                'Количество добавляемых пользователей',
                // Значение по-умолчанию (вместо null), если опция не была передана во время команды
                10,
            )
            ->addOption(
                // Имя опции
                'posts-number',
                // Сокращённое имя
                'p',
                // Значение этой опции является обязательным (сама опция по-прежнему необязательна)
                InputOption::VALUE_REQUIRED,
                // Описание
                'Количество добавляемых статей',
                // Значение по-умолчанию (вместо null), если опция не была передана во время команды
                20,
            )
            ->addOption(
                // Имя опции
                'comments-number',
                // Сокращённое имя
                'c',
                // Значение этой опции является обязательным (сама опция по-прежнему необязательна)
                InputOption::VALUE_REQUIRED,
                // Описание
                'Количество добавляемых комментариев',
                // Значение по-умолчанию (вместо null), если опция не была передана во время команды
                3,
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output,
    ): int
    {
        // Получаем необходимое количество создаваемых пользователей
        $usersNumber = (int)$input->getOption('users-number');
        // Получаем необходимое количество создаваемых статей
        $postsNumber = (int)$input->getOption('posts-number');
        // Получаем необходимое количество создаваемых статей
        $commentsNumber = (int)$input->getOption('comments-number');

        // Выходим, если количество новых пользователей не является положительным числом
        if ($usersNumber < 1) {
            $output->writeln('Количество добавляемых пользователей должно быть положительным числом');
            return Command::FAILURE;
        }

        // Выходим, если количество новых статей является отрицательным числом
        if ($postsNumber < 0) {
            $output->writeln('Количество добавляемых статей должно быть неотрицательным числом');
            return Command::FAILURE;
        }

        // Выходим, если количество новых комментариев является отрицательным числом
        if ($commentsNumber < 0) {
            $output->writeln('Количество добавляемых комментариев должно быть неотрицательным числом');
            return Command::FAILURE;
        }

        $users = [];

        // Создаём пользователей
        for ($i = 1; $i <= $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln("$i. Пользователь создан: " . $user->getUsername());
        }

        $posts = [];

        foreach ($users as $i => $user) {
            $userNumber = $i + 1;

            // От имени каждого пользователя создаём статьи
            for ($j = 1; $j <= $postsNumber; $j++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln("$userNumber-$j. Статья создана: " . $post->getTitle());
            }
        }

        foreach ($posts as $i => $post) {
            $postNumber = $i + 1;

            // Для каждой статьи создаём комментарии
            for ($j = 1; $j <= $commentsNumber; $j++) {
                // Авторы комментариев - случайные ранее созданные пользователи
                $author = $users[mt_rand(0, $usersNumber - 1)];

                $comment = $this->createFakeComment($author, $post);
                $output->writeln("$postNumber-$j. Комментарий создан: " . $comment->getText());
            }
        }

        $output->writeln("Новых пользователей: $usersNumber");
        $output->writeln("Новых статей: " . $usersNumber * $postsNumber);
        $output->writeln("Новых комментариев: " . $usersNumber * $postsNumber * $commentsNumber);

        return Command::SUCCESS;
    }

    /**
     * Создать фейкового пользователя
     * @throws InvalidArgumentException
     */
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            new Name(
                // Генерируем имя
                $this->faker->firstName,
                // Генерируем фамилию
                $this->faker->lastName
            )
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        return $user;
    }

    /**
     * Создать фейковую статью
     * @throws InvalidArgumentException
     */
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее 6 слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);

        return $post;
    }

    /**
     * Создать фейковый комментарий
     * @throws InvalidArgumentException
     */
    private function createFakeComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
            $this->faker->realText,
        );

        $this->commentsRepository->save($comment);

        return $comment;
    }

}
