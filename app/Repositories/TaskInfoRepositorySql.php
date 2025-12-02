<?php

namespace App\Repositories;

use App\Models\TaskInfo;

class TaskInfoRepositorySql extends BaseRepositorySql implements TaskInfoRepositoryInterface
{
    public function __construct(TaskInfo $model)
    {
        $this->model = $model;
    }
}
