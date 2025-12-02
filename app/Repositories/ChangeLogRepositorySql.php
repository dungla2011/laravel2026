<?php

namespace App\Repositories;

use App\Models\ChangeLog;

class ChangeLogRepositorySql extends BaseRepositorySql implements ChangeLogRepositoryInterface
{
    public function __construct(ChangeLog $model)
    {
        $this->model = $model;
    }
}
