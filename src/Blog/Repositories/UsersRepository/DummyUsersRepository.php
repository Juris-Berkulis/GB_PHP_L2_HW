<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;

// Dummy - чучуло, манекен
class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        // Ничего не делаем
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        // Для теста не важно, что это будет за пользователь,
        // поэтому возвращаем совершенно произвольного
        return new User(
            UUID::random(),
            new Name("first", "last"),
            "user123",
            'some_password',
        );
    }
}
