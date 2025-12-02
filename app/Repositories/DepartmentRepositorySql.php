<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepositorySql extends BaseRepositorySql implements DepartmentRepositoryInterface
{
    public function __construct(Department $model)
    {
        $this->model = $model;
    }
}
