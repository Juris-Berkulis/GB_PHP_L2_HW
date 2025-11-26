<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

readonly class JsonBodyUuidIdentification implements IdentificationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function getUserByUuid(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // Пытаемся создать UUID пользователя из данных запроса
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса - бросаем исключение
            throw new AuthException($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден - бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }

    public function getUserByUsername(Request $request): User
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
