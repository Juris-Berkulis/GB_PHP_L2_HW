<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

/**
 * Контракт получения пользователя из api-запроса
 */
interface AuthenticationInterface
{

    /**
     * Получить пользователя
     * @param Request $request
     * @return User
     * @throws AuthException
     */
    public function getUser(Request $request): User;

}
