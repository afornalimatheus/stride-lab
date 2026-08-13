<?php

namespace App\Actions\Admin\Users;

use App\Http\Requests\Admin\Users\ListUserRequest;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class ListUserAction
{
    public function execute(ListUserRequest $request): Paginator
    {
        $showInactive = $request->input('filter.active') === '0';

        $baseQuery = $showInactive
            ? User::withTrashed()->whereNotNull('users.deleted_at')
            : User::query();

        $query = SpatieQueryBuilder::for($baseQuery, request())
            ->with('globalRole')
            ->allowedFilters(
                AllowedFilter::callback('name', function (Builder $query, $value) {
                    $query->where('users.name', 'like', "%{$value}%");
                }),
                AllowedFilter::callback('role', function (Builder $query, $value) {
                    $query->whereExists(function (QueryBuilder $sub) use ($value) {
                        $sub->select(DB::raw(1))
                            ->from('organization_user')
                            ->join('roles as ror', 'ror.id', '=', 'organization_user.role_id')
                            ->whereColumn('organization_user.user_id', 'users.id')
                            ->where('ror.name', $value);
                    });
                }),
                AllowedFilter::callback('organization', function (Builder $query, $value) {
                    $query->whereHas('organizations', function (Builder $q) use ($value) {
                        $q->where('organizations.name', 'like', '%'.((string) $value).'%');
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
