<?php

namespace App\Repositories;

use App\Models\DepartmentEvent;

class DepartmentEventRepositorySql extends BaseRepositorySql implements DepartmentEventRepositoryInterface
{
    public function __construct(DepartmentEvent $model)
    {
        $this->model = $model;
    }
}
