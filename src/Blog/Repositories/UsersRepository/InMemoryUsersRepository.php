<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface
{

    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            // Сравниваем строковые представления UUID
            if ((string)$user->getUuid() === (string)$uuid) {
                return $user;
            }
        }

        throw new UserNotFoundException("Пользователь с id='$uuid' не найден");
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }

        throw new UserNotFoundException("Пользователь не найден: $username");
    }

}
