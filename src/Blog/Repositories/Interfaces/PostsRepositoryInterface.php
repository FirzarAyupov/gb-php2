<?php

namespace GeekBrains\Blog\Repositories\Interfaces;

use GeekBrains\Blog\Post;
use GeekBrains\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
}