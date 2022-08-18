<?php
namespace GeekBrains\http\Actions\Posts;


use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\http\Actions\ActionInterface;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\Request;
use GeekBrains\http\Response;
use GeekBrains\http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUUID = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $this->postsRepository->get(new UUID($postUUID));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->delPost(new UUID($request->query('uuid')));
        return new SuccessfulResponse();
    }
}
