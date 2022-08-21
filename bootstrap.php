<?php

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\Blog\Repositories\Interfaces\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\LikeRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\Repositories\LikeRepository\SqliteLikeRepository;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);
$container->bind(
    LikeRepositoryInterface::class,
    SqliteLikeRepository::class
);
// Возвращаем объект контейнера
return $container;