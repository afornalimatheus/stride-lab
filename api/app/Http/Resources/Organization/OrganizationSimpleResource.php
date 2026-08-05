<?php

namespace App\Http\Resources\Organization;

use App\Http\Resources\JsonResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CreateOrganizationUserResource",
 *     title="Create Organization User Resource",
 *     type="object",
 *
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="User unique identifier"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="User name"
 *     ),
 *     @OA\Property(
 *         property="organization_type",
 *         type="string",
 *         format="string",
 *         description="Organization type"
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="string",
 *         format="string",
 *         description="Active"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CreateOrganizationUserResponse",
 *     title="Create Organization User Response",
 *     type="object",
 *
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/CreateOrganizationUserResource"
 *     )
 * )
 *
 * @mixin User
 */
class OrganizationSimpleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'organization_type' => $this->organization_type,
            'active' => $this->active,
        ];
    }
}
