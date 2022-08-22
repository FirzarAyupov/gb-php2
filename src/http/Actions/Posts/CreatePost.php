<?php
namespace GeekBrains\http\Actions\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\UUID;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\http\Actions\ActionInterface;
use GeekBrains\http\Auth\IdentificationInterface;
use GeekBrains\http\Request;
use GeekBrains\http\Response;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
// Внедряем репозитории статей и пользователей
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Request $request): Response
    {

        $author = $this->identification->user($request);
// Генерируем UUID для новой статьи
        $newPostUuid = UUID::random();
        try {
// Пытаемся создать объект статьи
// из данных запроса
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
}
// Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
