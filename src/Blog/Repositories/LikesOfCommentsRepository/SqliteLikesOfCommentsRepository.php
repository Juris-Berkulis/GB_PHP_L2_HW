<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikesNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\LikeComment;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;

readonly class SqliteLikesOfCommentsRepository implements LikesOfCommentsRepositoryInterface
{

    public function __construct(private PDO $connection)
    {
    }

    function save(LikeComment $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes_of_comments (uuid, user_uuid, comment_uuid) VALUES (:uuid, :user_uuid, :comment_uuid)'
        );

        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':user_uuid' => (string)$like->getUserUuid(),
            ':comment_uuid' => (string)$like->getCommentUuid(),
        ]);
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
            throw new LikesNotFoundException("Лайки для комментария $commentUuid не найдены");
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
            throw new LikeAlreadyExist("Лайк от пользователя $userUuid для комментария $commentUuid уже существует");
        }
    }

}
