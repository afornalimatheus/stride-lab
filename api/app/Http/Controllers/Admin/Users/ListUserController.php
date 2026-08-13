<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\Admin\Users\ListUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\ListUserRequest;
use App\Http\Resources\Admin\User\ListUserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class ListUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/dashboard/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[role]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[organization]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="string", enum={"0","1"})),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users listed successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ListUsersResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse")
     * )
     */
    public function __invoke(
        ListUserAction $action,
        ListUserRequest $request,
    ): AnonymousResourceCollection {
        $users = $action->execute($request);

        return ListUserResource::collection($users);
    }
}
