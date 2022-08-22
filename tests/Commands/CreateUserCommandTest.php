<?php

namespace Commands;

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\Repositories\UsersRepository\DummyUsersRepository;
use GeekBrains\Blog\UnitTests\DummyLogger;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(
// Передаём наш стаб в качестве реализации UsersRepositoryInterface
            new DummyUsersRepository()
        );
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('User already exists: Ivan');
        $command->handle(new Arguments(['username' => 'Ivan']));
    }


    // Функция возвращает объект типа UsersRepositoryInterface
    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
// Тестовая реализация логгера
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
        ]));
    }

// Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
// Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
// Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {

                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository);

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }

}