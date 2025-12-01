<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Auth;

use DateTimeImmutable;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokenNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokensRepositoryException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

    private const string HEADER_PREFIX = 'Bearer ';

    /**
     * @param AuthTokensRepositoryInterface $authTokensRepository Репозиторий токенов
     * @param UsersRepositoryInterface $usersRepository Репозиторий пользователей
     */
    public function __construct(
        private readonly AuthTokensRepositoryInterface $authTokensRepository,
        private readonly UsersRepositoryInterface      $usersRepository,
    )
    {
    }

    public function getToken(Request $request): string
    {
        try {
            // Получаем HTTP-заголовок
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Неправильный формат токена: [$header]");
        }

        // Отрезаем префикс Bearer
        return mb_substr($header, strlen(self::HEADER_PREFIX));
    }

    public function getUser(Request $request): User
    {
        $token = self::getToken($request);

        try {
            // Ищем токен в репозитории
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Неверный токен: [$token]");
        } catch (AuthTokensRepositoryException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем срок годности токена
        if ($authToken->getExpiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Срок действия токена истек: [$token]");
        }

        // Получаем UUID пользователя из токена
        $userUuid = $authToken->getUserUuid();

        try {
            // Ищем и возвращаем пользователя
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }

}
