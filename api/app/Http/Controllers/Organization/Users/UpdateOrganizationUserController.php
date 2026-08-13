<?php

namespace App\Http\Controllers\Organization\Users;

use App\Actions\Organization\Users\UpdateOrganizationUserAction;
use App\DTOs\Organization\User\UpdateOrganizationUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Users\UpdateOrganizationUserRequest;
use App\Http\Resources\Organization\User\UpdateOrganizationUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class UpdateOrganizationUserController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/api/dashboard/users/{userId}",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrganizationUserRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrganizationUserResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntityResponse")
     * )
     */
    public function __invoke(
        UpdateOrganizationUserAction $action,
        UpdateOrganizationUserRequest $request,
        string $userId,
    ): JsonResource {
        $data = new UpdateOrganizationUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role,
        );

        $user = $action->execute($data, $userId, $request->user());

        return UpdateOrganizationUserResource::make($user);
    }
}
