<?php

namespace App\Repositories;

use App\Models\PlanDefineValue;

class PlanDefineValueRepositorySql extends BaseRepositorySql implements PlanDefineValueRepositoryInterface
{
    public function __construct(PlanDefineValue $model)
    {
        $this->model = $model;
    }
}
