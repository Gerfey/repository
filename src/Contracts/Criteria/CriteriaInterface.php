<?php

namespace Gerfey\Repository\Contracts\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface CriteriaInterface
{
    /**
     * @param $model
     *
     * @return Builder
     */
    public function apply($model): Builder;
}
