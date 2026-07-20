<?php

namespace App\Http\Resources\Role;

use App\Http\Resources\JsonResource;
use App\Models\Role;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="RoleResource",
 *     title="Role Resource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Role unique identifier"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Role name"
 *     )
 * )
 *
 * @mixin Role
 */
class RoleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
