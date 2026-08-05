<?php

namespace App\Actions;

use App\Http\Requests\PaginationRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;

trait Paginates
{
    protected function paginate(PaginationRequest $request, Builder $query): Paginator
    {
        return $query->paginate(
            perPage: $request->getLimit(),
            page: $request->getPage(),
        );
    }
}
