<?php

namespace App\Repositories;

use App\Models\HrJobTitle;

class HrJobTitleRepositorySql extends BaseRepositorySql implements HrJobTitleRepositoryInterface
{
    public function __construct(HrJobTitle $model)
    {
        $this->model = $model;
    }
}
