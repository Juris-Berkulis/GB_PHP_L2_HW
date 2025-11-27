<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts;

use JsonException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\AuthenticationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

readonly class CreatePost implements ActionInterface
{

    public function __construct(
        // Внедряем репозитории статей и пользователей
        private PostsRepositoryInterface $postsRepository,
        // Внедряем контракт идентификации
        private AuthenticationInterface  $authentication,
        // Внедряем контракт логгера
        private LoggerInterface          $logger,
    ) {
    }

    /**
     * @throws JsonException|InvalidArgumentException
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        try {
            // Аутентификация пользователя - автора статьи
            $user = $this->authentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        /**
         * UUID новой статьи
         */
        $newPostUuid = UUID::random();

        try {
            // Пытаемся создать объект статьи из данных запроса
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        // Логируем UUID новой статьи
        $this->logger->info("Статья создана: $newPostUuid");

        // Возвращаем успешный ответ, содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }

}
