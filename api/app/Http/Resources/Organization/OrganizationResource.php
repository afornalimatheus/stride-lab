<?php

namespace App\Http\Resources\Organization;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CreateOrganizationUserResource",
 *     title="Create Organization User Resource",
 *     type="object",
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
 *     @OA\Property(
 *         property="data",
 *         ref="#/components/schemas/CreateOrganizationUserResource"
 *     )
 * )
 *
 * @mixin \App\Models\User
 */
class OrganizationResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'owner' => $this->owner,
            'address' => $this->address,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'organization_type' => $this->organization_type,
            'active' => $this->active,
        ];
    }
}
