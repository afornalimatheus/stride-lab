<?php

namespace App\Http\Controllers\Organization\Users;

use App\Actions\Organization\Users\ListOrganizationUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Users\ListOrganizationUserRequest;
use App\Http\Resources\Organization\User\ListOrganizationUserResource;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class ListOrganizationUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/organizations/{organization}/users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="organization",
     *         in="path",
     *         required=true,
     *         description="Organization ID",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search in title and description",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[modality_ids]",
     *         in="query",
     *         required=false,
     *         description="Filter by modality IDs (comma-separated)",
     *
     *         @OA\Schema(type="string", example="id1,id2,id3")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[process_ids]",
     *         in="query",
     *         required=false,
     *         description="Filter by related process IDs (comma-separated)",
     *
     *         @OA\Schema(type="string", example="id1,id2")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[start_date]",
     *         in="query",
     *         required=false,
     *         description="Filter by exact start date",
     *
     *         @OA\Schema(type="string", format="date", example="2000-01-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[end_date]",
     *         in="query",
     *         required=false,
     *         description="Filter by exact end date",
     *
     *         @OA\Schema(type="string", format="date", example="2000-12-31")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[workload]",
     *         in="query",
     *         required=false,
     *         description="Filter by exact workload value",
     *
     *         @OA\Schema(type="integer", example=40)
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         required=false,
     *         description="Filter by status",
     *
     *         @OA\Schema(type="string", enum={"draft", "pending_approval", "approved", "published", "unpublished", "completed"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="filter[member_ids]",
     *         in="query",
     *         required=false,
     *         description="Filter by creator/editor user IDs (comma-separated)",
     *
     *         @OA\Schema(type="string", example="id1,id2")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ListOrganizationUsersResponse")
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFoundResponse"),
     *     @OA\Response(response=422, ref="#/components/responses/UnprocessableEntityResponse")
     * )
     */
    public function __invoke(
        ListOrganizationUserAction $action,
        ListOrganizationUserRequest $request,
        Organization $organization,
    ): JsonResource {
        $users = $action->execute(request: $request, organization: $organization);

        return ListOrganizationUserResource::collection($users);
    }
}
