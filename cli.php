<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Person\Name;
use GeekBrains\LevelTwo\Person\Person;

use GeekBrains\LevelTwo\Blog\Repositories\InMemoryUsersRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;

try {
    $post = new Post(
        new Person(
            new Name('Иван', 'Никитин'),
            new DateTimeImmutable()
        ),
        'Всем привет!' . PHP_EOL
    );

    print $post;

    $rep = new InMemoryUsersRepository();
    $user1 = new User(1, "Ember Song", "Ember");
    $user2 = new User(2, "Иван Иванов", "Ivan");

    $rep->save($user1);
    $rep->save($user2);

    echo $rep->get(1);
    echo $rep->get(2);
    echo $rep->get(23);

} catch (UserNotFoundException $exception) {
    echo $exception->getMessage();
} catch (\Exception $exception) {
    print_r($exception->getTrace());
}

