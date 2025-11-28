<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Auth;

use DateTimeImmutable;
use JurisBerkulis\GbPhpL2Hw\Blog\AuthToken;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokensRepositoryException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\TokenAuthenticationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;

readonly class LogOut implements ActionInterface
{

    public function __construct(
        // Внедряем контракт аутентификации
        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface   $authTokensRepository,
    )
    {
    }

    /**
     * @throws AuthTokensRepositoryException
     */
    public function handle(Request $request): Response
    {
        try {
            $token = $this->authentication->getToken($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Аутентификация пользователя, который хочет выйти
            $user = $this->authentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем токен
        $authToken = new AuthToken(
            $token,
            $user->getUuid(),
            new DateTimeImmutable(),
        );

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);

        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }

}
