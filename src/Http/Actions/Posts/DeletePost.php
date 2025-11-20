<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\ActionInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;

readonly class DeletePost implements ActionInterface
{

    public function __construct(private PostsRepositoryInterface $postsRepository)
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = new UUID($request->query('uuid'));
        } catch (InvalidArgumentException | HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->delete($postUuid);

        return new SuccessfulResponse([
            'uuid' => (string)$postUuid,
        ]);
    }

}
