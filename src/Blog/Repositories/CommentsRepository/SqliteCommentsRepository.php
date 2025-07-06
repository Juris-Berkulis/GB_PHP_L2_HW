<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommentNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    private PDO $connection;

    private PostsRepositoryInterface $postsRepository;

    private UsersRepositoryInterface $usersRepository;

    public function __construct(
        PDO $connection,
        PostsRepositoryInterface $postsRepository,
        UsersRepositoryInterface $usersRepository,
    )
    {
        $this->connection = $connection;
        $this->postsRepository = $postsRepository;
        $this->usersRepository = $usersRepository;
    }

    public function save(Comment $comment): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, user_uuid, text) VALUES (:uuid, :post_uuid, :user_uuid, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid' => (string)$comment->getPost()->getUuid(),
            ':user_uuid' => (string)$comment->getUser()->getUuid(),
            ':text' => $comment->getText(),
        ]);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    private function getComment(PDOStatement $statement, string $uniqueField): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Комментарий не найден: $uniqueField"
            );
        }

        // Создаём объект комментария
        return new Comment(
            new UUID($result['uuid']),
            $this->usersRepository->get(new UUID($result['user_uuid'])),
            $this->postsRepository->get(new UUID($result['post_uuid'])),
            $result['text'],
        );
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

}
