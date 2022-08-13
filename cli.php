<?php

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';


    //Создаём объект подключения к SQLite
    $connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');



//Создаём объект репозитория
    $usersRepository = new SqliteUsersRepository($connection);
    $postsRepository = new SqlitePostsRepository($connection);

    $command = new CreateUserCommand($usersRepository);
//    $command = new CreatePostCommand($postsRepository);

try {
    $command->handle(Arguments::fromArgv($argv));

    $user = $usersRepository->getByUsername('ivan');
   print $user;

} catch (Exception $exception) {
    echo $exception->getMessage();
}
