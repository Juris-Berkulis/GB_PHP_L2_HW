<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikesNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;

readonly class SqliteLikesOfPostsRepository implements LikesOfPostsRepositoryInterface
{

    public function __construct(private PDO $connection)
    {
    }

    function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes_of_posts (uuid, user_uuid, post_uuid) VALUES (:uuid, :user_uuid, :post_uuid)'
        );

        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':user_uuid' => (string)$like->getUserUuid(),
            ':post_uuid' => (string)$like->getPostUuid(),
        ]);
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
            throw new LikesNotFoundException("Лайки для статьи $postUuid не найдены");
        }

        $likes = [];

        foreach ($result as $like) {
            $likes[] = new Like(
                new UUID($like['uuid']),
                new UUID($like['user_uuid']),
                new UUID($like['post_uuid']),
            );
        }

        return $likes;
    }

}
