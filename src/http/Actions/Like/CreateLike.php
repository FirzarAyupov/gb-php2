<?php
namespace GeekBrains\http\Actions\Like;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Exceptions\LikeAlreadyCreatedException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Like;
use GeekBrains\Blog\Repositories\Interfaces\LikeRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\UUID;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\Exceptions\PostNotFoundException;
use GeekBrains\http\Actions\ActionInterface;
use GeekBrains\http\Request;
use GeekBrains\http\Response;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\SuccessfulResponse;

class CreateLike implements ActionInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * @throws LikeAlreadyCreatedException
     */
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $author = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        if ($this->likeRepository->checkLikeExist($postUuid, $authorUuid))
        {
            return new ErrorResponse("Like for the post $postUuid already been created");
        }

        $newLikeUuid = UUID::random();
        try {

            $like = new Like(
                $newLikeUuid,
                $post,
                $author
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
}
        $this->likeRepository->save($like);
        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}
