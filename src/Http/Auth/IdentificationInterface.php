<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

/**
 * // Контракт описывает единственный метод, получающий пользователя из запроса
 */
interface IdentificationInterface
{

    /**
     * Получить пользователя из запроса
     *
     * @param Request $request
     * @return User
     */
    public function user(Request $request): User;

}
