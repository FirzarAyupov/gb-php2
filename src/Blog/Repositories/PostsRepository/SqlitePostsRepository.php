<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\UUID;
use \GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
// Бросаем исключение, если пост не найден
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot get post: $uuid"
            );
        }

        return $this->getPost($statement, $uuid);
    }


    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
VALUES (:uuid, :author_uuid, :title, :text)'
        );


// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->author()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text(),
        ]);
    }


    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find post: $uuid"
            );
        }

        $userRepo = new SqliteUsersRepository($this->connection);
        $author = $userRepo->get($result('author_uuid'));

        return new Post(
            new UUID($result['uuid']),
            $author,
            $result['title'],
            $result['text'],
        );
    }
}