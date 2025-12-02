<?php

namespace App\Repositories;

use App\Models\MoneyAndTag;

class MoneyAndTagRepositorySql extends BaseRepositorySql implements MoneyAndTagRepositoryInterface
{
    public function __construct(MoneyAndTag $model)
    {
        $this->model = $model;
    }
}
