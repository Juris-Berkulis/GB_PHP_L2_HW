<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\LikeComment;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface LikesOfCommentsRepositoryInterface
{

    /**
     * Сохранить лайк
     * @param LikeComment $like
     * @return void
     */
    function save(LikeComment $like): void;

    /**
     * Получить все лайки комментария
     * @param UUID $commentUuid
     * @return array
     */
    function getByCommentUuid(UUID $commentUuid): array;

    /**
     * Проверить, существует ли лайк конкретного пользователя для конкретного комментария
     * @param UUID $userUuid
     * @param UUID $commentUuid
     * @throws LikeAlreadyExist
     */
    function checkLikeAlreadyExist(UUID $userUuid, UUID $commentUuid);

}
