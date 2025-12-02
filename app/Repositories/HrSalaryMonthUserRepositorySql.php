<?php

namespace App\Repositories;

use App\Models\HrSalaryMonthUser;

class HrSalaryMonthUserRepositorySql extends BaseRepositorySql implements HrSalaryMonthUserRepositoryInterface
{
    public function __construct(HrSalaryMonthUser $model)
    {
        $this->model = $model;
    }
}
