<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands\Users;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Изменить пользователя
 *
 * Изменяет имя и фамилию пользователя
 *
 * Принимает UUID пользователя в качестве аргумента,
 * а новые значения для имени и фамилии — как опции:
 */
class UpdateUser extends Command
{

    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Изменить пользователя')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID изменяемого пользователя'
            )
            ->addOption(
                // Имя опции
                'first-name',
                // Сокращённое имя
                'f',
                // Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                // Описание
                'Имя пользователя',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'фамилия пользователя',
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
        // Получаем значения опций
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        // Выходим, если обе опции пусты
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Нечего изменять');
            return Command::SUCCESS;
        }

        // Получаем UUID из аргумента
        $uuid = new UUID($input->getArgument('uuid'));

        // Получаем пользователя из репозитория
        $user = $this->usersRepository->get($uuid);

        // Создаём объект обновлённого имени
        $updatedName = new Name(
            // Берём сохранённое имя, если опция имени пуста
            firstName: empty($firstName) ? $user->getName()->getFirstName() : $firstName,
            // Берём сохранённую фамилию, если опция фамилии пуста
            lastName: empty($lastName) ? $user->getName()->getLastName() : $lastName,
        );

        // Создаём новый объект пользователя
        $updatedUser = new User(
            uuid: $uuid,
            // Обновлённое имя
            name: $updatedName,
            // Имя пользователя и пароль оставляем без изменений
            login: $user->getUsername(),
            hashedPassword: $user->getHashedPassword(),
        );

        // Сохраняем обновлённого пользователя
        $this->usersRepository->save($updatedUser);

        $output->writeln("Пользователь изменён: $uuid");

        return Command::SUCCESS;
    }
}
