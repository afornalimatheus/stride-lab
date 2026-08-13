<?php

namespace App\Http\Controllers\Organization\Users;

use App\Actions\Organization\Users\DeleteOrganizationUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class DeleteOrganizationUserController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/dashboard/users/{userId}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=204, description="User removed from organization successfully"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse")
     * )
     */
    public function __invoke(
        DeleteOrganizationUserAction $action,
        Request $request,
        string $userId,
    ): Response {
        $action->execute($userId, $request->user());

        return response()->noContent();
    }
}
