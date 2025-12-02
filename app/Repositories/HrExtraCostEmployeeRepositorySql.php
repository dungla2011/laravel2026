<?php

namespace App\Repositories;

use App\Models\HrExtraCostEmployee;

class HrExtraCostEmployeeRepositorySql extends BaseRepositorySql implements HrExtraCostEmployeeRepositoryInterface
{
    public function __construct(HrExtraCostEmployee $model)
    {
        $this->model = $model;
    }
}
