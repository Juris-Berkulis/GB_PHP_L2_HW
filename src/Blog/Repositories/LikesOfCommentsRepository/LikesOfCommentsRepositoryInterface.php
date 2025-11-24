<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface LikesOfCommentsRepositoryInterface
{

    /**
     * Сохранить лайк
     * @param Like $like
     * @return void
     */
    function save(Like $like): void;

    /**
     * Получить все лайки комментария
     * @param UUID $commentUuid
     * @return array
     */
    function getByCommentUuid(UUID $commentUuid): array;

}
