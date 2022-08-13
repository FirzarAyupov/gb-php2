<?php

namespace GeekBrains\Blog\Repositories\Interfaces;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $user): void;
    public function get(UUID $uuid): Comment;
}