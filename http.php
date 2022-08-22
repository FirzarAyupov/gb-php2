<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Exceptions\AppException;
use GeekBrains\http\Actions\Comments\CreateComment;
use GeekBrains\http\Actions\Likes\CreateLike;
use GeekBrains\http\Actions\Posts\CreatePost;
use GeekBrains\http\Actions\Posts\DeletePost;
use GeekBrains\http\Actions\Posts\FindByUuid;
use GeekBrains\http\Actions\Users\CreateUser;
use GeekBrains\http\ErrorResponse;
use GeekBrains\http\Request;
use GeekBrains\http\Actions\Users\FindByUsername;
use GeekBrains\Exceptions;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
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
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();


    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];
// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
}
$response->send();