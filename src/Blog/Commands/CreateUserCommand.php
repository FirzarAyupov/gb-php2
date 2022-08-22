<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use GeekBrains\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
// Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
        }

        $uuid = UUID::random();
        $this->usersRepository->save(new User(
            $uuid,
            $username,
            new Name($arguments->get('first_name'), $arguments->get('last_name'))
        ));

        $this->logger->info("User created: $uuid");
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