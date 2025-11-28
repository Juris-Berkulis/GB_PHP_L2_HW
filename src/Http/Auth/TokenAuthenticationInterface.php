<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

interface TokenAuthenticationInterface extends AuthenticationInterface
{

    /**
     * Получить токен
     * @param Request $request
     * @return string
     * @throws AuthException
     */
    public function getToken(Request $request): string;

}
