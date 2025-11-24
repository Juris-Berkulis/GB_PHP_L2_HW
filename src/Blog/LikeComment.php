<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class LikeComment extends Like
{

    public function __construct(
        UUID                  $uuid,
        UUID                  $userUuid,
        private readonly UUID $commentUuid,
    )
    {
        parent::__construct($uuid, $userUuid);
    }

    public function __toString(): string
    {
        return "Лайк с id $this->uuid от пользователя с id $this->userUuid поставлен комментарию с id $this->commentUuid";
    }

    public function getCommentUuid(): UUID
    {
        return $this->commentUuid;
    }

}
