<?php

namespace App\Repositories;

use App\Models\HrExpenseColMng;

class HrExpenseColMngRepositorySql extends BaseRepositorySql implements HrExpenseColMngRepositoryInterface
{
    public function __construct(HrExpenseColMng $model)
    {
        $this->model = $model;
    }
}
