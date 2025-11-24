<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class Like
{

    public function __construct(
        private readonly UUID $uuid,
        private UUID          $userUuid,
        private UUID          $postUuid,
    )
    {
    }

    public function __toString(): string
    {
        return "Лайк с id $this->uuid от пользователя с id $this->userUuid поставлен статье с id $this->postUuid";
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    public function setUserUuid(UUID $userUuid): void
    {
        $this->userUuid = $userUuid;
    }

    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    public function setPostUuid(UUID $postUuid): void
    {
        $this->postUuid = $postUuid;
    }

}
