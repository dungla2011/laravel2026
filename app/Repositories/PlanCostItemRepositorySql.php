<?php

namespace App\Repositories;

use App\Models\PlanCostItem;

class PlanCostItemRepositorySql extends BaseRepositorySql implements PlanCostItemRepositoryInterface
{
    public function __construct(PlanCostItem $model)
    {
        $this->model = $model;
    }
}
