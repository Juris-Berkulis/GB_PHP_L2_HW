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
     * Получить пользователя из запроса по uuid
     *
     * @deprecated Используется метод getUserByUsername
     *
     * @see getUserByUsername
     *
     * @param Request $request
     * @return User
     * @throws AuthException
     */
    public function getUserByUuid(Request $request): User;

    /**
     * Получить пользователя из запроса по username
     *
     * @param Request $request
     * @return User
     * @throws AuthException
     */
    public function getUserByUsername(Request $request): User;

}
