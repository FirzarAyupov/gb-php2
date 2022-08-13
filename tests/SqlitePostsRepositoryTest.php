<?php

use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use GeekBrains\Person\Name;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    /**
     * @throws \GeekBrains\Blog\Exceptions\CommentNotFoundException
     * @throws \GeekBrains\Blog\Exceptions\InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);
        $this->expectException(PostNotFoundException::class);
        $uuid = new UUID('123e4567-e89b-12d3-a456-426614174000');
        $this->expectExceptionMessage("Cannot get post: $uuid");

        $repository->get($uuid);

    }

    public function testItGetPostFromDatabase(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);
        $this->expectException(PostNotFoundException::class);
        $uuid = new UUID('123e4567-e89b-12d3-a456-426614174000');
        $this->expectExceptionMessage("Cannot get post: $uuid");

        $repository->get($uuid);

    }

    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesPostToDatabase(): void
    {
// 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
// 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);
// 5. Описываем ожидаемое взаимодействие
// нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '321e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Загаловок',
                ':text' => 'Текст статьи',
            ]);
// 3. При вызове метода prepare стаб подключения
// возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostsRepository($connectionStub);
        $user = new User(
            new UUID('321e4567-e89b-12d3-a456-426614174000'),
            'ivan123',
            new Name('Ivan', 'Nikitin')
        );
        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Загаловок',
                'Текст статьи',
            )
        );
    }
}