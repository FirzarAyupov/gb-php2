<?php

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use GeekBrains\Person\Name;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    /**
     * @throws \GeekBrains\Blog\Exceptions\CommentNotFoundException
     * @throws \GeekBrains\Blog\Exceptions\InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot get comment: 123e4567-e89b-12d3-a456-426614174000");

        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

    }


    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => 'a7db6205-c14b-4b56-8eaf-233593883ab1',
                ':post_uuid' => 'cfa10b64-1b24-11ed-861d-0242ac120002',
                ':author_uuid' => 'b4f37ba9-da96-42db-8af1-27a4fe85f1da',
                ':text' => 'Комментарий к статье',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepository($connectionStub);

        $repository->save(
            new Comment(
                new UUID('a7db6205-c14b-4b56-8eaf-233593883ab1'),
                new Post(
                    new UUID('cfa10b64-1b24-11ed-861d-0242ac120002'),
                    new User(
                        new UUID('13cc094e-1475-413a-890c-218ad601538c'),
                        'pavel777',
                        new Name('Pavel', 'Zubkov')
                    ),
                    'Загаловок',
                    'Текст статьи',
                ),
                new User(
                    new UUID('b4f37ba9-da96-42db-8af1-27a4fe85f1da'),
                    'ivan123',
                    new Name('Ivan', 'Nikitin')
                ),
                'Комментарий к статье',
            )
        );
    }

    /**
     * @throws CommentNotFoundException
     * @throws \GeekBrains\Blog\Exceptions\InvalidArgumentException
     */
    public function testItGetCommentFromDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub
            ->method('fetch')
            ->willReturn([
                'uuid' => 'a7db6205-c14b-4b56-8eaf-233593883ab1',
                'author_uuid' => new UUID('cfa10b64-1b24-11ed-861d-0242ac120002'),
                'post_uuid' => new UUID('13cc094e-1475-413a-890c-218ad601538c'),
                'text' => 'Комментарий к статье',
                ]);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub);

        $expected = [
            'uuid' => 'a7db6205-c14b-4b56-8eaf-233593883ab1',
            'author_uuid' => new UUID('cfa10b64-1b24-11ed-861d-0242ac120002'),
            'post_uuid' => new UUID('13cc094e-1475-413a-890c-218ad601538c'),
            'text' => 'Комментарий к статье',
        ];
        $actual = $repository->get(
            new UUID('a7db6205-c14b-4b56-8eaf-233593883ab1'),
        );

        $this->assertSame($expected, $actual);


    }
}