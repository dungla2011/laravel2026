<?php

namespace App\Repositories;

use App\Models\HrUserExpense;

class HrUserExpenseRepositorySql extends BaseRepositorySql implements HrUserExpenseRepositoryInterface
{
    public function __construct(HrUserExpense $model)
    {
        $this->model = $model;
    }
}
