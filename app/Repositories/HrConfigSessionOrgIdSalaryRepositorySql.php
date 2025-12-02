<?php

namespace App\Repositories;

use App\Models\HrConfigSessionOrgIdSalary;

class HrConfigSessionOrgIdSalaryRepositorySql extends BaseRepositorySql implements HrConfigSessionOrgIdSalaryRepositoryInterface
{
    public function __construct(HrConfigSessionOrgIdSalary $model)
    {
        $this->model = $model;
    }
}
