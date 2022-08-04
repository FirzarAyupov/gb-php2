<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\User;

$faker = Faker\Factory::create();
$user = new User($faker->firstName, $faker->lastName, $faker->userName);
$post = new Post(text:$faker->paragraph($nbSentences = 3, $variableNbSentences = true));
$comment = new Comment(text:$faker->realText);

switch ($argv[1]) {
    case 'user':
        echo $user;
        break;
    case 'post':
        echo $post;
        break;
    case 'comment':
        echo $comment;
        break;
}

