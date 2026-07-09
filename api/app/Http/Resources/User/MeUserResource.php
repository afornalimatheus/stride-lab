<?php

namespace App\Http\Resources\User;

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
 *         property="nickname",
 *         type="string",
 *         description="User nickname"
 *     ),
 *     @OA\Property(
 *         property="enrollment_id",
 *         type="string",
 *         description="User enrollment/client identifier",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User email address"
 *     ),
 *     @OA\Property(
 *         property="roles",
 *         type="object",
 *         description="User roles organized by scope",
 *         @OA\Property(
 *             property="system",
 *             type="array",
 *             description="System-level roles (internal identifiers)",
 *             @OA\Items(
 *                 type="string",
 *                 example="SUPER_ADMIN"
 *             )
 *         ),
 *         @OA\Property(
 *             property="client",
 *             type="array",
 *             description="Client-level roles (internal identifiers)",
 *             @OA\Items(
 *                 type="string",
 *                 example="TEACHER"
 *             )
 *         )
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
    // /**
    //  * @var array{system: array<int, string>, client: array<int, string>}
    //  */
    // private array $roles = ['system' => [], 'client' => []];

    // /**
    //  * @param array{system: array<int, string>, client: array<int, string>} $roles
    //  */
    // public function withRoles(array $roles): static
    // {
    //     $this->roles = $roles;

    //     return $this;
    // }

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
