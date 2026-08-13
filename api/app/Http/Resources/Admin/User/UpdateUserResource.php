<?php

namespace App\Http\Resources\Admin\User;

use App\Http\Resources\JsonResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UpdateUserResource",
 *     title="Update User Resource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", description="User unique identifier"),
 *     @OA\Property(property="name", type="string", description="User name"),
 *     @OA\Property(property="email", type="string", format="email", description="User e-mail")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateUserResponse",
 *     title="Update User Response",
 *     type="object",
 *
 *     @OA\Property(property="data", ref="#/components/schemas/UpdateUserResource")
 * )
 *
 * @mixin User
 */
class UpdateUserResource extends JsonResource
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
        ];
    }
}
