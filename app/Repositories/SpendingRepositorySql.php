<?php

namespace App\Repositories;

use App\Models\Spending;

class SpendingRepositorySql extends BaseRepositorySql implements SpendingRepositoryInterface
{
    public function __construct(Spending $model)
    {
        $this->model = $model;
    }
}
