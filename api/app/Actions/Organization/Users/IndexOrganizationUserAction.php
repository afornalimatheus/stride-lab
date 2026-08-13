<?php

namespace App\Actions\Organization\Users;

use App\Http\Requests\Organization\Users\IndexOrganizationUserRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class IndexOrganizationUserAction
{
    public function execute(IndexOrganizationUserRequest $request, ?Organization $organization): Paginator
    {
        if (! $organization) {
            throw new \Exception('User does not belong to any organization.');
        }

        $showInactive = $request->input('filter.active') === '0';

        $baseQuery = User::withTrashed()
            ->whereHas('organizations', fn (Builder $q) => $q->where('organizations.id', $organization->id));

        if ($showInactive) {
            $baseQuery->whereNotNull('users.deleted_at');
        } else {
            $baseQuery->whereNull('users.deleted_at');
        }

        $query = SpatieQueryBuilder::for($baseQuery, request())
            ->with('globalRole')
            ->allowedFilters(
                AllowedFilter::callback('name', function (Builder $query, $value) {
                    $query->where('users.name', 'like', "%{$value}%");
                }),
                AllowedFilter::callback('role', function (Builder $query, $value) use ($organization) {
                    $query->whereExists(function (QueryBuilder $sub) use ($value, $organization) {
                        $sub->select(DB::raw(1))
                            ->from('organization_user')
                            ->join('roles', 'roles.id', '=', 'organization_user.role_id')
                            ->whereColumn('organization_user.user_id', 'users.id')
                            ->where('organization_user.organization_id', $organization->id)
                            ->where('roles.name', $value);
                    });
                }),
                AllowedFilter::callback('active', fn (Builder $q, $v) => $q),
            )
            ->defaultSort('-created_at');

        return $query->paginate(
            perPage: $request->getLimit(),
            page: $request->getPage(),
        );
    }
}
