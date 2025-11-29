<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands\FakeData;

use Faker\Generator;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        // Внедряем генератор тестовых данных и репозитории пользователей и статей
        private readonly Generator                $faker,
        private readonly UsersRepositoryInterface $usersRepository,
        private readonly PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Заполняет базу данных фейковыми данными');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $users = [];

        // Создаём 10 пользователей
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('Пользователь создан: ' . $user->getUsername());
        }

        foreach ($users as $user) {
            // От имени каждого пользователя создаём по 20 статей
            for ($i = 0; $i < 20; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Статья создана: ' . $post->getTitle());
            }
        }

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

}
