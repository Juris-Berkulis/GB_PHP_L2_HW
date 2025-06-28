<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function save(User $user): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name) VALUES (:uuid, :username, :first_name, :last_name)'

        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUsername(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $uniqueField): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new UserNotFoundException(
                "Пользователь не найден: $uniqueField"
            );
        }

        // Создаём объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }


}
