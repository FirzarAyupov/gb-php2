<?php

namespace GeekBrains\Blog\Repositories\Interfaces;

use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}