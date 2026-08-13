<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\Admin\Users\DeleteUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class DeleteUserController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/admin/dashboard/users/{userId}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=204, description="User deleted successfully"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse")
     * )
     */
    public function __invoke(
        DeleteUserAction $action,
        Request $request,
        string $userId,
    ): Response {
        $action->execute($userId);

        return response()->noContent();
    }
}
