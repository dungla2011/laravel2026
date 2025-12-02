<?php

namespace App\Repositories;

use App\Models\MoneyLog;

class MoneyLogRepositorySql extends BaseRepositorySql implements MoneyLogRepositoryInterface
{
    public function __construct(MoneyLog $model)
    {
        $this->model = $model;
    }
}
