<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use GeekBrains\Person\Name;

class CreateUserCommand
{
// Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }

// Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            new Name($arguments->get('first_name'), $arguments->get('last_name'))
        ));
    }

    private function userExists(string $username): bool
    {
        try {
// Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}