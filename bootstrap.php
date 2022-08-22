<?php

use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\http\Auth\JsonBodyUuidIdentification;
use GeekBrains\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\Blog\Repositories\Interfaces\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\LikesRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\http\Auth\IdentificationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(
    PDO::class,
// Берём путь до файла базы данных SQLite
// из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
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
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
);


$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

return $container;