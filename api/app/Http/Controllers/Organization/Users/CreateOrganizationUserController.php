<?php

namespace App\Http\Controllers\Organization\Users;

use App\Actions\Organization\Users\CreateOrganizationUserAction;
use App\DTOs\Organization\User\CreateOrganizationUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Users\CreateOrganizationUserRequest;
use App\Http\Resources\Organization\User\CreateOrganizationUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class CreateOrganizationUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/dashboard/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/CreateOrganizationUserRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/CreateUserResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntityResponse")
     * )
     */
    public function __invoke(
        CreateOrganizationUserAction $action,
        CreateOrganizationUserRequest $request,
    ): JsonResource {
        $data = new CreateOrganizationUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role,
        );

        $user = $action->execute($data, $request->user());

        return CreateOrganizationUserResource::make($user);
    }
}
