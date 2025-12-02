<?php

namespace App\Repositories;

use App\Models\Todo2;

class Todo2RepositorySql extends BaseRepositorySql implements Todo2RepositoryInterface
{
    public function __construct(Todo2 $model)
    {
        $this->model = $model;
    }
}
