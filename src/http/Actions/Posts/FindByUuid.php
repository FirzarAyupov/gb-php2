<?php

namespace GeekBrains\http\Actions\Posts;

use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;
use GeekBrains\Exceptions\PostNotFoundException;
use GeekBrains\http\Request;
use GeekBrains\http\Response;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\http\Actions\ActionInterface;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\SuccessfulResponse;

class FindByUuid implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $postUUID = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $post = $this->postsRepository->get(new UUID($postUUID));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'uuid' => $post->uuid(),
            'author_uuid' => $post->author()->uuid(),
            'title' => $post->title(),
            'text' => $post->text(),
        ]);
    }
}