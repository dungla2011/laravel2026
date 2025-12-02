<?php

namespace App\Repositories;

use App\Models\HrKpiCldv;

class HrKpiCldvRepositorySql extends BaseRepositorySql implements HrKpiCldvRepositoryInterface
{
    public function __construct(HrKpiCldv $model)
    {
        $this->model = $model;
    }
}
