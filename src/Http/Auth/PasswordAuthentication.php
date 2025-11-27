<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

readonly class PasswordAuthentication implements AuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function getUser(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            // Получаем имя пользователя из JSON-тела запроса;
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            // Если невозможно получить имя пользователя из запроса - бросаем исключение
            throw new AuthException($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким username не найден - бросаем исключение
            throw new AuthException($e->getMessage());
        }

        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль соответствует сохранённому в БД
        try {
            // Получаем пароль пользователя из JSON-тела запроса;
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            // Если невозможно получить пароль пользователя из запроса - бросаем исключение
            throw new AuthException($e->getMessage());
        }

        // Вычисляем SHA-256-хеш предъявленного пароля
        $passwordHash = hash('sha256', $password);

        if ($passwordHash !== $user->getPassword()) {
            // Если пароли не совпадают — бросаем исключение
            throw new AuthException('Неправильный пароль');
        }

        // Пользователь аутентифицирован
        return $user;

    }
}
