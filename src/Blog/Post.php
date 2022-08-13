<?php

namespace GeekBrains\Blog;

use GeekBrains\Blog\UUID;

class Post
{
    public function __construct(
        private UUID $uuid,
        private User $author,
        private string $title,
        private string $text
    )
    {
    }

    /**
     * @return User
     */
    public function author(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function __toString()
    {
        return $this->title() . ' ' . $this->text();
    }
}
