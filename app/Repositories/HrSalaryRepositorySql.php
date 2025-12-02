<?php

namespace App\Repositories;

use App\Models\HrSalary;

class HrSalaryRepositorySql extends BaseRepositorySql implements HrSalaryRepositoryInterface
{
    public function __construct(HrSalary $model)
    {
        $this->model = $model;
    }
}
