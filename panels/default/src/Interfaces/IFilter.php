<?php


namespace App\DefaultPanel\Interfaces;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

interface IFilter {

    public function filter(Builder $builder, $value);
}
