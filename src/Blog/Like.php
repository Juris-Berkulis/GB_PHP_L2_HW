<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

abstract class Like
{

    public function __construct(
        protected readonly UUID $uuid,
        protected UUID          $userUuid,
    )
    {
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

}
