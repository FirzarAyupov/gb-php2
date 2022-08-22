<?php

namespace GeekBrains\Blog\Repositories\CommentsRepository;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Blog\UUID;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private \PDO $connection;

    public function __construct(
        \PDO $connection,
        private LoggerInterface $logger)
    {
        $this->connection = $connection;
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }


    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => (string)$comment->post()->uuid(),
            ':author_uuid' => (string)$comment->author()->uuid(),
            ':text' => $comment->text(),
        ]);
        $this->logger->info("Comment save to DB" . $comment->uuid());
    }


    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     * @throws \GeekBrains\Blog\Exceptions\UserNotFoundException
     */
    private function getComment(\PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $this->logger->warning("Comment already exists: $uuid");
            throw new CommentNotFoundException(
                "Cannot find comment: $uuid"
            );
        }

        $usersRepo = new SqliteUsersRepository($this->connection);
        $postsRepo = new SqlitePostsRepository($this->connection);

        return new Comment(
            new UUID($result['uuid']),
            $postsRepo->get($result['post_uuid']),
            $usersRepo->get($result['author_uuid']),
            $result['text'],
        );
    }
}