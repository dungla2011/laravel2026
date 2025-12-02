<?php

namespace App\Repositories;

use App\Models\HrTimeSheet;

class HrTimeSheetRepositorySql extends BaseRepositorySql implements HrTimeSheetRepositoryInterface
{
    public function __construct(HrTimeSheet $model)
    {
        $this->model = $model;
    }
}
