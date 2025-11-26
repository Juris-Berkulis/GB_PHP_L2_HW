<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Users;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

/**
 * Класс реализует контракт действия
 */
class FindByUsername implements ActionInterface
{

    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository,
        // Внедряем контракт логгера
        private LoggerInterface                   $logger
    ) {
    }

    /**
     * Вернуть информацию о пользователе по его username
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            $errorMessage = $e->getMessage();

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            // Если в запросе нет параметра username - возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($errorMessage);
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            $errorMessage = $e->getMessage();

            // Логируем сообщение с уровнем WARNING
            $this->logger->warning($errorMessage);

            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($errorMessage);
        }

        $username = $user->getUsername();

        // Логируем UUID новой статьи
        $this->logger->info("Пользователь найден по username: $username");

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $username,
            'name' => $user->getName()->getFirstName() . ' ' . $user->getName()->getLastName(),
        ]);
    }

}
