<?php

namespace App\Repositories;

use App\Models\HrJob;

class HrJobRepositorySql extends BaseRepositorySql implements HrJobRepositoryInterface
{
    public function __construct(HrJob $model)
    {
        $this->model = $model;
    }
}
