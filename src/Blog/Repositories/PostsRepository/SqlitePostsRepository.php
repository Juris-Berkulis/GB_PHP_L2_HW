<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface
{

    private PDO $connection;

    private UsersRepositoryInterface $usersRepository;

    public function __construct(
        PDO $connection,
        UsersRepositoryInterface $usersRepository
    )
    {
        $this->connection = $connection;
        $this->usersRepository = $usersRepository;
    }

    public function save(Post $post): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, user_uuid, title, text) VALUES (:uuid, :user_uuid, :title, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':user_uuid' => $post->getUser()->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(PDOStatement $statement, string $uniqueField): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Статья не найдена: $uniqueField"
            );
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
