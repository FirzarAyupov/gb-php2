<?php

namespace GeekBrains\http\Actions;

use GeekBrains\http\Request;
use GeekBrains\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}