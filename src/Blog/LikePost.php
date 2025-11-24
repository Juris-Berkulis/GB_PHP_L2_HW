<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class LikePost extends Like
{

    public function __construct(
        UUID                  $uuid,
        UUID                  $userUuid,
        private readonly UUID $postUuid,
    )
    {
        parent::__construct($uuid, $userUuid);
    }

    public function __toString(): string
    {
        return "Лайк с id $this->uuid от пользователя с id $this->userUuid поставлен статье с id $this->postUuid";
    }

    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

}
