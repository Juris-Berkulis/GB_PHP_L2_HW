<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use Psr\Log\LoggerInterface;

class InMemoryUsersRepository implements UsersRepositoryInterface
{

    private array $users = [];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function save(User $user): void
    {
        $this->users[] = $user;
        $userUuid = $user->getUuid();

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Пользователь сохранён: $userUuid");
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

        $errorMessage = "Пользователь с id='$uuid' не найден";

        // Логируем сообщение с уровнем WARNING
        $this->logger->warning($errorMessage);

        throw new UserNotFoundException($errorMessage);
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

        $errorMessage = "Пользователь не найден: $username";

        // Логируем сообщение с уровнем WARNING
        $this->logger->warning($errorMessage);

        throw new UserNotFoundException($errorMessage);
    }

}
