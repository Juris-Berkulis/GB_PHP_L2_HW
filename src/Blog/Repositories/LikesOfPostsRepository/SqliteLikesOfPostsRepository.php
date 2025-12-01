<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikesNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\LikePost;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use Psr\Log\LoggerInterface;

readonly class SqliteLikesOfPostsRepository implements LikesOfPostsRepositoryInterface
{

    public function __construct(
        private PDO             $connection,
        private LoggerInterface $logger,
    )
    {
    }

    function save(LikePost $like): void
    {
        $likeUuid = (string)$like->getUuid();

        $statement = $this->connection->prepare(
            'INSERT INTO likes_of_posts (uuid, user_uuid, post_uuid) VALUES (:uuid, :user_uuid, :post_uuid)'
        );

        $statement->execute([
            ':uuid' => $likeUuid,
            ':user_uuid' => (string)$like->getUserUuid(),
            ':post_uuid' => (string)$like->getPostUuid(),
        ]);

        // Логируем сообщение с уровнем INFO
        $this->logger->info("Лайк к статье сохранён: $likeUuid");
    }

    /**
     * @throws LikesNotFoundException
     * @throws InvalidArgumentException
     */
    function getByPostUuid(UUID $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_of_posts WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => (string)$postUuid,
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 0) {
            $errorMessage = "Лайки для статьи $postUuid не найдены";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new LikesNotFoundException($errorMessage);
        }

        $likes = [];

        foreach ($result as $like) {
            $likes[] = new LikePost(
                new UUID($like['uuid']),
                new UUID($like['user_uuid']),
                new UUID($like['post_uuid']),
            );
        }

        return $likes;
    }

    /**
     * @throws LikeAlreadyExist
     */
    function checkLikeAlreadyExist(UUID $userUuid, UUID $postUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_of_posts WHERE user_uuid = :user_uuid AND post_uuid = :post_uuid'
        );

        $statement->execute([
            ':user_uuid' => (string)$userUuid,
            ':post_uuid' => (string)$postUuid,
        ]);

        $result = $statement->fetch();

        if ($result) {
            $errorMessage = "Лайк от пользователя $userUuid для статьи $postUuid уже существует";

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            throw new LikeAlreadyExist($errorMessage);
        }
    }

}
