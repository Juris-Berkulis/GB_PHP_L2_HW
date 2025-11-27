<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;

    private LoggerInterface $logger;

    public function __construct(
        PDO $connection,
        LoggerInterface $logger,
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(User $user): void
    {
        $userUuid = (string)$user->getUuid();

        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            '
                INSERT INTO users (uuid, username, password, first_name, last_name)
                VALUES (:uuid, :username, :password, :first_name, :last_name)
            '
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $userUuid,
            ':username' => $user->getUsername(),
            ':password' => $user->getHashedPassword(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
        ]);

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Пользователь сохранён: $userUuid");
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, string $uniqueField): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $errorMessage = "Пользователь не найден: $uniqueField";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new UserNotFoundException($errorMessage);
        }

        // Создаём объект пользователя
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            $result['password'],
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
