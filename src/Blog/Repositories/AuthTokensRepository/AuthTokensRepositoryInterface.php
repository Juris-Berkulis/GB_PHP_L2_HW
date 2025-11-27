<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\AuthTokensRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\AuthToken;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokenNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokensRepositoryException;

interface AuthTokensRepositoryInterface
{

    /**
     * Сохранить токен
     * @param AuthToken $authToken
     * @return void
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void;

    /**
     * Получить токен
     * @param string $token
     * @return AuthToken
     * @throws AuthTokenNotFoundException
     * @throws AuthTokensRepositoryException
     */
    public function get(string $token): AuthToken;

}
