<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\Admin\Users\UpdateUserAction;
use App\DTOs\Admin\User\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use App\Http\Resources\Admin\User\UpdateUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class UpdateUserController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/api/admin/dashboard/users/{userId}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntityResponse")
     * )
     */
    public function __invoke(
        UpdateUserAction $action,
        UpdateUserRequest $request,
        string $userId,
    ): JsonResource {
        $data = new UpdateUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );

        $user = $action->execute($data, $userId);

        return UpdateUserResource::make($user);
    }
}
