<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands\Users;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{

    public function __construct(
        // Внедряем репозиторий пользователей
        private readonly UsersRepositoryInterface $usersRepository,
    ) {
        // Вызываем родительский конструктор
        parent::__construct();
    }

    /**
     * Метод для конфигурации команды
     * @return void
     */
    protected function configure(): void
    {
        $this
            // Указываем имя команды (используется для запуска команды)
            ->setName('users:create')
            // Описание команды
            ->setDescription('Создать нового пользователя')
            // Перечисляем аргументы команды
            ->addArgument(
                // Имя аргумента (его значение будет доступно по этому имени)
                'first_name',
                // Указание того, что аргумент обязательный
                InputArgument::REQUIRED,
                // Описание аргумента
                'Имя пользователя'
            )
            // Описываем остальные аргументы
            ->addArgument('last_name', InputArgument::REQUIRED, 'Фамилия пользователя')
            ->addArgument('username', InputArgument::REQUIRED, 'Логин пользователя')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль пользователя');
    }

    /**
     * Метод, который будет запущен при вызове команды
     *
     * В метод будет передан объект типа InputInterface,
     * содержащий значения аргументов;
     * и объект типа OutputInterface,
     * имеющий методы для форматирования и вывода сообщений
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        // Для вывода сообщения вместо логгера используем объект типа OutputInterface
        $output->writeln('Начата команда создания пользователя');

        // Вместо использования нашего класса Arguments
        // получаем аргументы из объекта типа InputInterface
        $username = $input->getArgument('username');

        if ($this->userExists($username)) {
            // Используем OutputInterface вместо логгера
            $output->writeln("Пользователь уже существует: $username");

            // Завершаем команду с ошибкой
            return Command::FAILURE;
        }

        // Аналогичен из класса CreateUserCommand
        // Вместо Arguments используем InputInterface
        $user = User::createFrom(
            $username,
            $input->getArgument('password'),
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name')
            )
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        // Используем OutputInterface вместо логгера
        $output->writeln('Пользователь создан с uuid: ' . $user->getUuid());

        // Возвращаем код успешного завершения
        return Command::SUCCESS;
    }

    /**
     * Проверить, существует ли пользователь
     *
     * Полностью аналогичен из класса CreateUserCommand
     *
     * @param string $username
     * @return bool
     */
    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
