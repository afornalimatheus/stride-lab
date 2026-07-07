<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Return the authenticated user's profile.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(auth('api')->user());
    }
}
