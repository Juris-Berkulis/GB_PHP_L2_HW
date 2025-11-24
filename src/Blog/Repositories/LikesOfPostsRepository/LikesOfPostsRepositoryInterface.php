<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\LikePost;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface LikesOfPostsRepositoryInterface
{

    /**
     * Сохранить лайк
     * @param LikePost $like
     * @return void
     */
    function save(LikePost $like): void;

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
