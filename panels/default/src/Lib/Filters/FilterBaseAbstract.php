<?php


namespace App\DefaultPanel\Lib\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Tasawk\AdminPanel\Interfaces\IFilter;

abstract class FilterBaseAbstract {
    /**
     * @var Request
     */
    private Request $request;

    protected $filters = [];
    protected $orderBy = [];

    public function __construct(Request $request) {
        $this->request = $request;
    }


    /**
     * @param Builder $builder
     * @return mixed
     */
    public function filter(Builder $builder) {
        foreach ($this->getFilters() as $filter => $value) {
            ($this->resolveFilter($filter))->filter($builder, $value);
        }
        return $builder;
    }


    public function addFilters($filters) {
        $this->filters = $filters + $this->filters;
        return $this;
    }

    /**
     * @param $filter
     * @return IFilter
     */
    private function resolveFilter($filter) {
        return new $this->filters[$filter];
    }

    public function getFilters($requestFilter = true) {
        if ($requestFilter === false) {
            return $this->filters;
        }
        return $this->filterFilters($this->filters);
    }

    private function filterFilters(array $filters) {
        return array_filter(
            $this->request->only(array_keys($filters)),
            fn($value) => ($value !== null && $value !== false && $value)
    );
    }

    /**
     * @param $order
     * @return IFilter
     */
    public function getOrderBy($requestOrder = true) {
        return $this->orderBy;
    }

}
