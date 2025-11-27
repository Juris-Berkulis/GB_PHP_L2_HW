<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfPosts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\LikePost;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository\LikesOfPostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\IdentificationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

readonly class CreateLikeOfPost implements ActionInterface
{

    public function __construct(
        // Внедряем контракт идентификации
        private IdentificationInterface         $identification,
        private PostsRepositoryInterface        $postsRepository,
        private LikesOfPostsRepositoryInterface $likesOfPostsRepository,
        // Внедряем контракт логгера
        private LoggerInterface          $logger,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        try {
            // Идентифицируем пользователя - автора статьи
            $user = $this->identification->getUserByUsername($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $userUuid = $user->getUuid();

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (InvalidArgumentException|HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesOfPostsRepository->checkLikeAlreadyExist($userUuid, $postUuid);
        } catch (LikeAlreadyExist $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new LikePost(
            $newLikeUuid,
            $userUuid,
            $postUuid,
        );

        $this->likesOfPostsRepository->save($like);

        // Логируем UUID нового лайка к статье
        $this->logger->info("Лайк к статье создан: $newLikeUuid");

        return new SuccessfulResponse([
            'uuid'=>(string)$newLikeUuid,
        ]);
    }

}
