<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Exceptions\AppException;
use GeekBrains\http\Actions\Comments\CreateComment;
use GeekBrains\http\Actions\Like\CreateLike;
use GeekBrains\http\Actions\Posts\CreatePost;
use GeekBrains\http\Actions\Posts\DeletePost;
use GeekBrains\http\Actions\Posts\FindByUuid;
use GeekBrains\http\Actions\Users\CreateUser;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\Request;
use GeekBrains\http\Actions\Users\FindByUsername;
use GeekBrains\Exceptions;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);
try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов
$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/users/create' => CreateUser::class,
        '/posts/comment' => CreateComment::class,
        '/posts/like' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];
// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();