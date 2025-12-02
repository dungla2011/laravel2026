<?php

namespace App\Repositories;

use App\Models\PlanName;

class PlanNameRepositorySql extends BaseRepositorySql implements PlanNameRepositoryInterface
{
    public function __construct(PlanName $model)
    {
        $this->model = $model;
    }
}
