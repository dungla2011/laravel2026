<?php

namespace App\Repositories;

use App\Models\HrTask;

class HrTaskRepositorySql extends BaseRepositorySql implements HrTaskRepositoryInterface
{
    public function __construct(HrTask $model)
    {
        $this->model = $model;
    }
}
