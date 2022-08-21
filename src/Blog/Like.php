<?php

namespace GeekBrains\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
    )
    {
    }

    public function post(): Post
    {
        return $this->post;
    }

    public function author(): User
    {
        return $this->author;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function __toString()
    {
        return $this->author . ' лайкнул: ' . $this->post;
    }
}
