<?php

namespace App\Http\Resources\Organization\User;

use App\Http\Resources\JsonResource;
use App\Http\Resources\Process\SimpleProcessResource;
use App\Http\Resources\Type\SimpleTypeResource;
use App\Http\Resources\User\SimpleUserResource;
use App\Models\Modality;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ListModalityItem",
 *     title="List Modality Item",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", description="Modality unique identifier"),
 *     @OA\Property(property="identifier", type="string", description="Modality unique identifier code"),
 *     @OA\Property(property="title", type="string", description="Modality title"),
 *     @OA\Property(property="start_date", type="string", description="Start date in UTC", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="end_date", type="string", description="End date in UTC", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="workload", type="integer", description="Workload hours", example=40),
 *     @OA\Property(property="status", type="string", description="Modality status", enum={"draft", "pending_approval", "approved", "published", "unpublished", "completed"}),
 *     @OA\Property(property="type", ref="#/components/schemas/Type"),
 *     @OA\Property(property="processes", type="array", @OA\Items(ref="#/components/schemas/SimpleProcess")),
 *     @OA\Property(property="creator", ref="#/components/schemas/SimpleUser"),
 *     @OA\Property(property="editors", type="array", @OA\Items(ref="#/components/schemas/SimpleUser"))
 * )
 *
 * @OA\Schema(
 *     schema="ListModalitiesResponse",
 *     title="List Modalities Response",
 *     type="object",
 *
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/ListModalityItem")
 *     ),
 *
 *     @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @mixin Modality
 */
class ListOrganizationUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'workload' => $this->workload,
            'status' => $this->status,
            'type' => SimpleTypeResource::make($this->whenLoaded('type')),
            'processes' => SimpleProcessResource::collection($this->whenLoaded('processes')),
            'creator' => SimpleUserResource::make($this->whenLoaded('creator')),
            'editors' => SimpleUserResource::collection($this->whenLoaded('editors')),
        ];
    }
}
