<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\JsonResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="MeUserResource",
 *     title="Me User Resource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *         description="User unique identifier"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="User full name"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User email address"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="MeUserResponse",
 *     title="Me User Response",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/MeUserResource"
 *     )
 * )
 *
 * @mixin User
 */
class MeUserResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
