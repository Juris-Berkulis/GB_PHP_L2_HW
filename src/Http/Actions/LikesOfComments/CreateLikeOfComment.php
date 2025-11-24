<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfComments;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommentNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfCommentsRepository\LikesOfCommentsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;

readonly class CreateLikeOfComment implements ActionInterface
{

    public function __construct(
        private UsersRepositoryInterface           $usersRepository,
        private CommentsRepositoryInterface        $commentsRepository,
        private LikesOfCommentsRepositoryInterface $likesOfCommentsRepository,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (InvalidArgumentException|HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

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

        $like = new Like(
            $newLikeUuid,
            $userUuid,
            $commentUuid,
        );

        $this->likesOfCommentsRepository->save($like);

        return new SuccessfulResponse([
            'uuid'=>(string)$newLikeUuid,
        ]);
    }

}
