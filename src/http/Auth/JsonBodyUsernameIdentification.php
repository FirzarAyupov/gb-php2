<?php
namespace GeekBrains\http\Auth;

use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Exceptions\AuthException;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\Exceptions\UserNotFoundException;
use GeekBrains\http\Auth\IdentificationInterface;
use GeekBrains\http\Request;
use GeekBrains\Blog\User;

class JsonBodyUsernameIdentification implements IdentificationInterface

{
    public function __construct(
    private UsersRepositoryInterface $usersRepository
) {
}
    public function user(Request $request): User
{
    try {
// Получаем имя пользователя из JSON-тела запроса;
// ожидаем, что имя пользователя находится в поле username
        $username = $request->jsonBodyField('username');
    } catch (HttpException $e) {
// Если невозможно получить имя пользователя из запроса -
// бросаем исключение
        throw new AuthException($e->getMessage());
    }
    try {
// Ищем пользователя в репозитории и возвращаем его
        return $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// бросаем исключение
        throw new AuthException($e->getMessage());
    }
}
}