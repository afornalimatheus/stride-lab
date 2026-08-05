<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTGuard;

abstract class Controller
{
    protected function jwtGuard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        return $guard;
    }
}
