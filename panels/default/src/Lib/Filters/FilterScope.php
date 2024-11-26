<?php


namespace App\DefaultPanel\Lib\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\DefaultPanel\Lib\Filters\FilterBaseAbstract;

trait FilterScope {

    function scopeFilter(Builder $builder, Request $request, string $filterClass, array $filters = []) {
        /** @var FilterBaseAbstract $filter */
        $filter = new $filterClass($request);
        if ($filter instanceof FilterBaseAbstract) {
            return $filter->addFilters($filters)->filter($builder);
        }
    }

    function scopeFiltered(): Builder|\App\DefaultPanel\Lib\Filters\FilterBaseAbstract {
        return $this->filter(request(),$this->filterClass);
    }
}
