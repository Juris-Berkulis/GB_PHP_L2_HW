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
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    private PDO $connection;

    private PostsRepositoryInterface $postsRepository;

    private UsersRepositoryInterface $usersRepository;

    private LoggerInterface $logger;

    public function __construct(
        PDO $connection,
        PostsRepositoryInterface $postsRepository,
        UsersRepositoryInterface $usersRepository,
        LoggerInterface $logger,
    )
    {
        $this->connection = $connection;
        $this->postsRepository = $postsRepository;
        $this->usersRepository = $usersRepository;
        $this->logger = $logger;
    }

    public function save(Comment $comment): void
    {
        $commentUuid = (string)$comment->getUuid();

        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, user_uuid, text) VALUES (:uuid, :post_uuid, :user_uuid, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $commentUuid,
            ':post_uuid' => (string)$comment->getPost()->getUuid(),
            ':user_uuid' => (string)$comment->getUser()->getUuid(),
            ':text' => $comment->getText(),
        ]);

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Комментарий сохранён: $commentUuid");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    private function getComment(PDOStatement $statement, string $uniqueField): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $errorMessage = "Комментарий не найден: $uniqueField";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new CommentNotFoundException($errorMessage);
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
