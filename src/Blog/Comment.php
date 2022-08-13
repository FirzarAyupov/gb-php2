<?php

namespace GeekBrains\Blog;

use GeekBrains\Blog\UUID;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
        private string $text
    )
    {
    }

    /**
     * @return Post
     */
    public function post(): Post
    {
        return $this->post;
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
        return $this->author . ' пишет: ' . $this->text;
    }
}
