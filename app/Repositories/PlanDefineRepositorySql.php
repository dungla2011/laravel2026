<?php

namespace App\Repositories;

use App\Models\PlanDefine;

class PlanDefineRepositorySql extends BaseRepositorySql implements PlanDefineRepositoryInterface
{
    public function __construct(PlanDefine $model)
    {
        $this->model = $model;
    }
}
