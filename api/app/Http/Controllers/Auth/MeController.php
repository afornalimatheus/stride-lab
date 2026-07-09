<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User\MeUserResource;

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
