<?php

namespace GeekBrains\Blog\Repositories\UsersRepository;

use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use GeekBrains\Person\Name;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUsername(string $username): User
    {
        return new User(UUID::random(), "user123", new Name("first", "last"));
    }
}