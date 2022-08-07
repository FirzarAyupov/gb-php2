<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Name;
use GeekBrains\LevelTwo\Blog\UUID;

class User
{

    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $name
    )
    {
    }

    public function username(): string
    {
        return $this->username;
    }

    public function __toString(): string
    {
        $firstName = $this->name()->first();
        $lastName = $this->name()->last();
        return "Пользователь $firstName $lastName" . PHP_EOL;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }


    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }




}