<?php

namespace App\Repositories;

use App\Models\UserRecharge;

class UserRechargeRepositorySql extends BaseRepositorySql implements UserRechargeRepositoryInterface
{
    public function __construct(UserRecharge $model)
    {
        $this->model = $model;
    }
}
