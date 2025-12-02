<?php

namespace App\Repositories;

use App\Models\PayMoneylog;

class PayMoneylogRepositorySql extends BaseRepositorySql implements PayMoneylogRepositoryInterface
{
    public function __construct(PayMoneylog $model)
    {
        $this->model = $model;
    }
}
