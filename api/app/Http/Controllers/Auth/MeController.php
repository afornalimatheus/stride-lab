<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\MeUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Return the authenticated user's profile.
     */
    public function __invoke(Request $request): MeUserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new MeUserResource($user);
    }
}
