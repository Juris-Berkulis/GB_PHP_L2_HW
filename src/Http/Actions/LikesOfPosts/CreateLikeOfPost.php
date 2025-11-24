<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\LikesOfPosts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\LikeAlreadyExist;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Like;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\LikesOfPostsRepository\LikesOfPostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;

readonly class CreateLikeOfPost implements ActionInterface
{

    public function __construct(
        private UsersRepositoryInterface        $usersRepository,
        private PostsRepositoryInterface        $postsRepository,
        private LikesOfPostsRepositoryInterface $likesOfPostsRepository,
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

        $like = new Like(
            $newLikeUuid,
            $userUuid,
            $postUuid,
        );

        $this->likesOfPostsRepository->save($like);

        return new SuccessfulResponse([
            'uuid'=>(string)$newLikeUuid,
        ]);
    }

}
