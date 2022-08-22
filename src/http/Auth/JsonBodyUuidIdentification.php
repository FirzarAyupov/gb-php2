<?php
namespace GeekBrains\Blog\http\Auth;

use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use GeekBrains\Blog\UUID;
use GeekBrains\Exceptions\HttpException;
use GeekBrains\Exceptions\InvalidArgumentException;
use GeekBrains\http\Auth\IdentificationInterface;
use GeekBrains\http\Request;
use GeekBrains\Blog\User;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }
    public function user(Request $request): User
    {
        try {
// Получаем UUID пользователя из JSON-тела запроса;
// ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
// Если невозможно получить UUID из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
// Если пользователь с таким UUID не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
