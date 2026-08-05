<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\Admin\Users\CreateUserAction;
use App\DTOs\Admin\User\CreateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\CreateUserRequest;
use App\Http\Resources\Admin\User\CreateUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class CreateUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/dashboard/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
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
        CreateUserAction $action,
        CreateUserRequest $request,
    ): JsonResource {
        $data = new CreateUserDTO(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role,
        );

        $user = $action->execute($data, $request->user());

        return CreateUserResource::make($user);
    }
}
