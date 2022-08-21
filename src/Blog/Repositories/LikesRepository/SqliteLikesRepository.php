<?php

namespace GeekBrains\Blog\Repositories\LikesRepository;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\LikesRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\UUID;
use GeekBrains\Blog\Like;
use PDO;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getLike($statement, $uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE $post_uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$postUuid,
        ]);

        return $this->getLike($statement, $postUuid, false);
    }

    public function checkLikeExist(UUID $postUuid, UUID $authorUuid): bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid AND author_uuid = :author_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string)$postUuid,
            ':author_uuid' => (string)$authorUuid,
        ]);

        if ($statement->fetch()) {
            return true;
        }
        return false;
    }


    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid)
VALUES (:uuid, :post_uuid, :author_uuid)'
        );

        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':post_uuid' => (string)$like->post()->uuid(),
            ':author_uuid' => (string)$like->author()->uuid(),
        ]);
    }


    /**
     * @throws InvalidArgumentException
     * @throws LikeNotFoundException
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     */
    private function getLike(\PDOStatement $statement, string $uuid): Like
    {
        $like = $statement->fetch(PDO::FETCH_ASSOC);
        if ($like === false) {
            throw new LikeNotFoundException(
                "Cannot find like: $uuid"
            );
        }

        $usersRepo = new SqliteUsersRepository($this->connection);
        $postsRepo = new SqlitePostsRepository($this->connection);

        $result = new Like(
            new UUID($like['uuid']),
            $postsRepo->get($like['post_uuid']),
            $usersRepo->get($like['author_uuid']),
        );

        return $result;
    }
}