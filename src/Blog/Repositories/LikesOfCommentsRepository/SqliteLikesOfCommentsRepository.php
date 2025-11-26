<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikesNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\LikeComment;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use Psr\Log\LoggerInterface;

readonly class SqliteLikesOfCommentsRepository implements LikesOfCommentsRepositoryInterface
{

    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger,
    )
    {
    }

    function save(LikeComment $like): void
    {
        $likeUuid = (string)$like->getUuid();

        $statement = $this->connection->prepare(
            'INSERT INTO likes_of_comments (uuid, user_uuid, comment_uuid) VALUES (:uuid, :user_uuid, :comment_uuid)'
        );

        $statement->execute([
            ':uuid' => $likeUuid,
            ':user_uuid' => (string)$like->getUserUuid(),
            ':comment_uuid' => (string)$like->getCommentUuid(),
        ]);

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Лайк к комментарию сохранён: $likeUuid");
    }

    /**
     * @throws LikesNotFoundException
     * @throws InvalidArgumentException
     */
    function getByCommentUuid(UUID $commentUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_of_comments WHERE comment_uuid = :comment_uuid'
        );

        $statement->execute([
            ':comment_uuid' => (string)$commentUuid,
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 0) {
            $errorMessage = "Лайки для комментария $commentUuid не найдены";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new LikesNotFoundException($errorMessage);
        }

        $likes = [];

        foreach ($result as $like) {
            $likes[] = new LikeComment(
                new UUID($like['uuid']),
                new UUID($like['user_uuid']),
                new UUID($like['comment_uuid']),
            );
        }

        return $likes;
    }

    /**
     * @throws LikeAlreadyExist
     */
    function checkLikeAlreadyExist(UUID $userUuid, UUID $commentUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_of_comments WHERE user_uuid = :user_uuid AND comment_uuid = :comment_uuid'
        );

        $statement->execute([
            ':user_uuid'=>(string)$userUuid,
            ':comment_uuid'=>(string)$commentUuid,
        ]);

        $result = $statement->fetch();

        if ($result) {
            $errorMessage = "Лайк от пользователя $userUuid для комментария $commentUuid уже существует";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new LikeAlreadyExist($errorMessage);
        }
    }

}
