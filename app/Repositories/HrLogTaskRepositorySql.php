<?php

namespace App\Repositories;

use App\Models\HrLogTask;

class HrLogTaskRepositorySql extends BaseRepositorySql implements HrLogTaskRepositoryInterface
{
    public function __construct(HrLogTask $model)
    {
        $this->model = $model;
    }
}
