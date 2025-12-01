<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\ArgumentsException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommandException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use Psr\Log\LoggerInterface;

readonly class CreateUserCommand
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommandException|ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена (Уровень логирования – INFO)
        $this->logger->info('Начата команда создания пользователя');

        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            $errorMessage = "Пользователь уже существует: $username";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            // Бросаем исключение, если пользователь уже существует
            throw new CommandException($errorMessage);
        }

        // Создаём объект пользователя
        // Метод createFrom сам создаст UUID и захеширует пароль
        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        // Логируем информацию о новом пользователе
        $this->logger->info('Пользователь создан с uuid' . $user->getUuid());
    }

    /**
     * Проверить, существует ли пользователь
     * @param string $username
     * @return bool
     */
    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }

}
