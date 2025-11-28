<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Auth;

use DateMalformedStringException;
use DateTimeImmutable;
use JurisBerkulis\GbPhpL2Hw\Blog\AuthToken;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokensRepositoryException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\PasswordAuthenticationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Random\RandomException;

readonly class LogIn implements ActionInterface
{

    /**
     * @param PasswordAuthenticationInterface $passwordAuthentication Авторизация по паролю
     * @param AuthTokensRepositoryInterface $authTokensRepository Репозиторий токенов
     */
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface   $authTokensRepository
    ) {
    }

    /**
     * @throws AuthTokensRepositoryException
     * @throws DateMalformedStringException
     * @throws RandomException
     */
    public function handle(Request $request): Response
    {
        try {
            // Аутентифицируем пользователя
            $user = $this->passwordAuthentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);

        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }
}
