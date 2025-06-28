<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\ArgumentsException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommandException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;

readonly class CreateUserCommand
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommandException|ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("Пользователь уже существует: $username");
        }

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name'),
            ),
            $username,
        ));
    }

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
