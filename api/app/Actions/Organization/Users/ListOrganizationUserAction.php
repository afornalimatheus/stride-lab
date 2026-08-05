<?php

namespace App\Actions\Organization\Users;

use App\Actions\Paginates;
use App\Http\Requests\Organization\Users\ListOrganizationUserRequest;
use App\Models\Organization;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ListOrganizationUserAction
{
    use Paginates;

    public function execute(ListOrganizationUserRequest $request, Organization $organization): Paginator
    {
        /** @phpstan-ignore-next-line */
        $query = QueryBuilder::for($organization->users())
            ->select([
                'id',
                'name',
                'email',
                'role_id',
                'created_at',
                'updated_at',
            ])
            ->with([
                'role:id,name',
                'creator:id,name,avatar_url',
                'editors:id,name,avatar_url',
            ])
            ->allowedFilters([
                AllowedFilter::callback('modality_ids', function (Builder $query, $value) {
                    $ids = is_array($value) ? $value : explode(',', $value);
                    $query->whereIn('modalities.id', $ids);
                }),
                AllowedFilter::callback('process_ids', function (Builder $query, $value) {
                    $ids = is_array($value) ? $value : explode(',', $value);
                    $query->whereHas('processes', function (Builder $q) use ($ids) {
                        $q->whereIn('processes.id', $ids);
                    });
                }),
                AllowedFilter::exact('start_date'),
                AllowedFilter::exact('end_date'),
                AllowedFilter::exact('workload'),
                AllowedFilter::exact('status'),
                AllowedFilter::callback('member_ids', function (Builder $query, $value) {
                    $ids = is_array($value) ? $value : explode(',', $value);
                    $query->where(function (Builder $q) use ($ids) {
                        $q->whereIn('created_by', $ids)
                            ->orWhereHas('editors', function (Builder $subQuery) use ($ids) {
                                $subQuery->whereIn('users.id', $ids);
                            });
                    });
                }),
            ])
            ->when($request->search, function (Builder $query, $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->whereLike('title', "%{$search}%")
                        ->orWhereLike('description', "%{$search}%")
                        ->orWhereLike('identifier', "%{$search}%");
                });
            })
            ->defaultSort('-created_at');

        return $this->paginate($request, $query);
    }
}
