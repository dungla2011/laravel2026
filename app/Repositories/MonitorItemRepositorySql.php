<?php

namespace App\Repositories;

use App\Models\MonitorItem;

class MonitorItemRepositorySql extends BaseRepositorySql implements MonitorItemRepositoryInterface
{
    public function __construct(MonitorItem $model)
    {
        $this->model = $model;
    }
}
