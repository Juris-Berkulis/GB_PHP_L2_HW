<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\TokenAuthenticationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

readonly class DeletePost implements ActionInterface
{

    public function __construct(
        // Внедряем контракт аутентификации
        private TokenAuthenticationInterface  $authentication,
        private PostsRepositoryInterface $postsRepository,
        // Внедряем контракт логгера
        private LoggerInterface          $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            // Аутентификация пользователя - автора статьи
            $user = $this->authentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->query('uuid'));
        } catch (InvalidArgumentException | HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if ($post->getUser()->getHashedPassword() !== $user->getHashedPassword()) {
            return new ErrorResponse('Нельзя удалять чужую статью');
        }

        $this->postsRepository->delete($postUuid);

        // Логируем UUID удалённой статьи
        $this->logger->info("Статья удалена: $postUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$postUuid,
        ]);
    }

}
