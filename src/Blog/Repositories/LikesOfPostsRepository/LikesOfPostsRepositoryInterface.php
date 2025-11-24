<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface LikesOfPostsRepositoryInterface
{

    /**
     * Сохранить лайк
     * @param Like $like
     * @return void
     */
    function save(Like $like): void;

    /**
     * Получить все лайки статьи
     * @param UUID $postUuid
     * @return array
     */
    function getByPostUuid(UUID $postUuid): array;

    /**
     * Проверить, существует ли лайк конкретного пользователя для конкретной статьи
     * @param UUID $userUuid
     * @param UUID $postUuid
     * @throws LikeAlreadyExist
     */
    function checkLikeAlreadyExist(UUID $userUuid, UUID $postUuid);

}
