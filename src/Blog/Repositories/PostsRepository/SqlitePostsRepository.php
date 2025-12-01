<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{

    private PDO $connection;

    private UsersRepositoryInterface $usersRepository;

    private LoggerInterface $logger;

    public function __construct(
        PDO                      $connection,
        UsersRepositoryInterface $usersRepository,
        LoggerInterface          $logger,
    )
    {
        $this->connection = $connection;
        $this->usersRepository = $usersRepository;
        $this->logger = $logger;
    }

    public function save(Post $post): void
    {
        $postUuid = (string)$post->getUuid();

        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, user_uuid, title, text) VALUES (:uuid, :user_uuid, :title, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $postUuid,
            ':user_uuid' => $post->getUser()->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Статья сохранена: $postUuid");
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(PDOStatement $statement, string $uniqueField): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $errorMessage = "Статья не найдена: $uniqueField";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new PostNotFoundException($errorMessage);
        }

        // Создаём объект статьи
        return new Post(
            new UUID($result['uuid']),
            $this->usersRepository->get(new UUID($result['user_uuid'])),
            $result['title'],
            $result['text'],
        );
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }

}
