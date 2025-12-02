<?php

namespace App\Repositories;

use App\Models\HrSessionType;

class HrSessionTypeRepositorySql extends BaseRepositorySql implements HrSessionTypeRepositoryInterface
{
    public function __construct(HrSessionType $model)
    {
        $this->model = $model;
    }
}
