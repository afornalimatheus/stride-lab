<?php

namespace App\Http\Resources\Organization\User;

use App\Http\Resources\JsonResource;
use App\Http\Resources\Organization\OrganizationSimpleResource;
use App\Http\Resources\Role\RoleResource;
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
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User e-mail"
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
class CreateOrganizationUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'organization' => OrganizationSimpleResource::make($this->currentOrganization()),
            'role' => RoleResource::make($this->role()),
        ];
    }
}
