<?php

namespace App\Repositories;

use App\Models\HrContract;

class HrContractRepositorySql extends BaseRepositorySql implements HrContractRepositoryInterface
{
    public function __construct(HrContract $model)
    {
        $this->model = $model;
    }
}
