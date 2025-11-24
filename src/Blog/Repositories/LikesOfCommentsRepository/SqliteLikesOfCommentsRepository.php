<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikesNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;

readonly class SqliteLikesOfCommentsRepository implements LikesOfCommentsRepositoryInterface
{

    public function __construct(private PDO $connection)
    {
    }

    function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes_of_comments (uuid, user_uuid, comment_uuid) VALUES (:uuid, :user_uuid, :comment_uuid)'
        );

        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':user_uuid' => $like->getUserUuid(),
            ':comment_uuid' => $like->getPostUuid(),
        ]);
    }

    /**
     * @throws LikesNotFoundException
     */
    function getByCommentUuid(UUID $commentUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes_of_comments WHERE comment_uuid = :comment_uuid'
        );

        $statement->execute([
            ':comment_uuid' => $commentUuid,
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 0) {
            throw new LikesNotFoundException("Лайки для комментария $commentUuid не найдены");
        }

        $likes = [];

        foreach ($result as $like) {
            $likes[] = new Like(
                $like['uuid'],
                $like['user_uuid'],
                $like['comment_uuid'],
            );
        }

        return $likes;
    }

}
