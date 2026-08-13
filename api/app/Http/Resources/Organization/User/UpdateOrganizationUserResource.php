<?php

namespace App\Http\Resources\Organization\User;

use App\Http\Resources\JsonResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UpdateOrganizationUserResource",
 *     title="Update Organization User Resource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", description="User unique identifier"),
 *     @OA\Property(property="name", type="string", description="User name"),
 *     @OA\Property(property="email", type="string", format="email", description="User e-mail"),
 *     @OA\Property(property="role", ref="#/components/schemas/RoleResource")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateOrganizationUserResponse",
 *     title="Update Organization User Response",
 *     type="object",
 *
 *     @OA\Property(property="data", ref="#/components/schemas/UpdateOrganizationUserResource")
 * )
 *
 * @mixin User
 */
class UpdateOrganizationUserResource extends JsonResource
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
            'role' => RoleResource::make($this->role()),
        ];
    }
}
