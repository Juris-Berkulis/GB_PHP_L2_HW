<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Auth;

use DateTimeImmutable;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokenNotFoundException;
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
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException|AuthTokensRepositoryException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken->setExpiresOn(new DateTimeImmutable());

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);

        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }

}
