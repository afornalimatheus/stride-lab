<?php

namespace App\Http\Controllers\Organization\Users;

use App\Actions\Organization\Users\IndexOrganizationUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Users\IndexOrganizationUserRequest;
use App\Http\Resources\Organization\User\IndexOrganizationUserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class IndexOrganizationUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[name]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[role]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[active]", in="query", required=false, @OA\Schema(type="string", enum={"0","1"})),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users listed successfully",
     *
     *         @OA\JsonContent(ref="#/components/schemas/IndexOrganizationUsersResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=403, ref="#/components/responses/ForbiddenResponse")
     * )
     */
    public function __invoke(
        IndexOrganizationUserAction $action,
        IndexOrganizationUserRequest $request,
    ): AnonymousResourceCollection {
        $organization = $request->user()->currentOrganization();

        $users = $action->execute($request, $organization);

        return IndexOrganizationUserResource::collection($users);
    }
}
