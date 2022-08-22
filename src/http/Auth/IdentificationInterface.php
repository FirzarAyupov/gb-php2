<?php

namespace GeekBrains\http\Auth;

use GeekBrains\Blog\User;
use GeekBrains\http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}
