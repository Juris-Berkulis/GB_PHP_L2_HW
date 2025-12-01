<?php

namespace JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\HttpException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\Response;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindByUuid
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface          $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            $errorMessage = $e->getMessage();

            $this->logger->warning($errorMessage);

            return new ErrorResponse($errorMessage);
        }

        try {
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException|InvalidArgumentException $e) {
            $errorMessage = $e->getMessage();

            $this->logger->warning($errorMessage);

            return new ErrorResponse($errorMessage);
        }

        $this->logger->info("Статья найдена по uuid: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$post->getUuid(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
            'author uuid' => (string)$post->getUser()->getUuid(),
            'author username' => (string)$post->getUser()->getUsername(),
            'author first name' => (string)$post->getUser()->getName()->getFirstName(),
            'author last name' => (string)$post->getUser()->getName()->getLastName(),
        ]);
    }

}
