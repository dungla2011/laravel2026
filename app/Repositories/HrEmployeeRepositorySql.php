<?php

namespace App\Repositories;

use App\Models\HrEmployee;

class HrEmployeeRepositorySql extends BaseRepositorySql implements HrEmployeeRepositoryInterface
{
    public function __construct(HrEmployee $model)
    {
        $this->model = $model;
    }
}
