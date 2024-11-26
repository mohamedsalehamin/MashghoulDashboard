<?php


namespace App\DefaultPanel\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Filterable {
    function scopeFilter(Builder $builder, Request $request, string $filterClass, array $filters = []);
}
