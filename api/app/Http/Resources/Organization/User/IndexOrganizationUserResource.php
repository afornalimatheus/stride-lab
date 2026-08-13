<?php

namespace App\Http\Resources\Organization\User;

use App\Http\Resources\JsonResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="IndexOrganizationUserItem",
 *     title="Index Organization User Item",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", description="User unique identifier"),
 *     @OA\Property(property="name", type="string", description="User name"),
 *     @OA\Property(property="email", type="string", format="email", description="User e-mail"),
 *     @OA\Property(property="role", ref="#/components/schemas/RoleResource"),
 *     @OA\Property(property="active", type="boolean", description="Whether the user is active")
 * )
 *
 * @OA\Schema(
 *     schema="IndexOrganizationUsersResponse",
 *     title="Index Organization Users Response",
 *     type="object",
 *
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/IndexOrganizationUserItem")),
 *     @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @mixin User
 */
class IndexOrganizationUserResource extends JsonResource
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
            'active' => is_null($this->deleted_at),
        ];
    }
}
