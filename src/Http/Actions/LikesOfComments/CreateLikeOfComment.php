<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfComments;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommentNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\LikeComment;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository\LikesOfCommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\TokenAuthenticationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

readonly class CreateLikeOfComment implements ActionInterface
{

    public function __construct(
        // Внедряем контракт идентификации
        private TokenAuthenticationInterface       $authentication,
        private CommentsRepositoryInterface        $commentsRepository,
        private LikesOfCommentsRepositoryInterface $likesOfCommentsRepository,
        // Внедряем контракт логгера
        private LoggerInterface                    $logger,
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
            $user = $this->authentication->getUser($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $userUuid = $user->getUuid();

        try {
            $commentUuid = new UUID($request->jsonBodyField('comment_uuid'));
        } catch (InvalidArgumentException|HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->commentsRepository->get($commentUuid);
        } catch (CommentNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesOfCommentsRepository->checkLikeAlreadyExist($userUuid, $commentUuid);
        } catch (LikeAlreadyExist $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new LikeComment(
            $newLikeUuid,
            $userUuid,
            $commentUuid,
        );

        $this->likesOfCommentsRepository->save($like);

        // Логируем UUID нового лайка к комментарию
        $this->logger->info("Лайк к комментарию создан: $newLikeUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }

}
