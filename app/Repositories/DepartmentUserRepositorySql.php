<?php

namespace App\Repositories;

use App\Models\DepartmentUser;

class DepartmentUserRepositorySql extends BaseRepositorySql implements DepartmentUserRepositoryInterface
{
    public function __construct(DepartmentUser $model)
    {
        $this->model = $model;
    }
}
