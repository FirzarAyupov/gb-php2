<?php

namespace GeekBrains\Blog\Repositories\Interfaces;

use GeekBrains\Blog\Like;
use GeekBrains\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function get(UUID $uuid): Like;
    public function checkLikeExist(UUID $postUuid, UUID $authorUuid): bool;
    public function getByPostUuid(UUID $postUuid): ?array;
}