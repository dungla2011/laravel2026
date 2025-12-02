<?php

namespace App\Repositories;

use App\Models\HrMessageTask;

class HrMessageTaskRepositorySql extends BaseRepositorySql implements HrMessageTaskRepositoryInterface
{
    public function __construct(HrMessageTask $model)
    {
        $this->model = $model;
    }
}
