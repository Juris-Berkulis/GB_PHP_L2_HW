<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Commands;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommandException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;

class CreateUserCommand
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     */
    public function handle(array $rawInput): void
    {
        $input = $this->parseRawInput($rawInput);
        $username = $input['username'];

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("Пользователь уже существует: $username");
        }

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            new Name($input['first_name'], $input['last_name']),
            $username,
        ));
    }

    // Из команды "php8.3 cli.php username=ivan first_name=Ivan last_name=Nikitin"
    // преобразуем входной массив из предопределённой переменной $argv
    // array(4) {
    // [0]=>
    // string(18) "cli.php"
    // [1]=>
    // string(13) "username=ivan"
    // [2]=>
    // string(15) "first_name=Ivan"
    // [3]=>
    // string(17) "last_name=Nikitin"
    // }
    //
    // в ассоциативный массив вида
    // array(3) {
    // ["username"]=>
    // string(4) "ivan"
    // ["first_name"]=>
    // string(4) "Ivan"
    // ["last_name"]=>
    // string(7) "Nikitin"
    //}
    /**
     * @throws CommandException
     */
    private function parseRawInput(array $rawInput): array
    {
        $input = [];

        foreach ($rawInput as $argument) {
            $parts = explode('=', $argument);

            if (count($parts) !== 2) {
                continue;
            }

            $input[$parts[0]] = $parts[1];
        }

        foreach (['username', 'first_name', 'last_name'] as $argument) {
            if (!array_key_exists($argument, $input)) {
                throw new CommandException(
                    "Не предоставлено требуемого аргумента: $argument"
                );
            }

            if (empty($input[$argument])) {
                throw new CommandException(
                    "Предоставлен пустой аргумент: $argument"
                );
            }
        }

        return $input;
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
