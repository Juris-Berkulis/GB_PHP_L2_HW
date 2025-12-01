<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

use DateTimeImmutable;

class AuthToken
{

    /**
     * @param string $token Строка токена
     * @param UUID $userUuid uuid пользователя
     * @param DateTimeImmutable $expiresOn Срок годности
     */
    public function __construct(
        private readonly string   $token,
        private readonly UUID     $userUuid,
        private DateTimeImmutable $expiresOn,
    )
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    public function getExpiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }

    public function setExpiresOn(DateTimeImmutable $expiresOn): void
    {
        $this->expiresOn = $expiresOn;
    }

}
