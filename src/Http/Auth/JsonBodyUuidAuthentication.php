<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

/**
 * @deprecated
 * @see PasswordAuthentication
 */
readonly class JsonBodyUuidAuthentication implements AuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function getUser(Request $request): User
    {
        try {
            // Получаем имя пользователя из JSON-тела запроса;
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            // Если невозможно получить имя пользователя из запроса - бросаем исключение
            throw new AuthException($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким username не найден - бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }

}
