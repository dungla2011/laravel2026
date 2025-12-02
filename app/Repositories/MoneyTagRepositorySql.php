<?php

namespace App\Repositories;

use App\Models\MoneyTag;

class MoneyTagRepositorySql extends BaseRepositorySql implements MoneyTagRepositoryInterface
{
    public function __construct(MoneyTag $model)
    {
        $this->model = $model;
    }
}
