<?php

namespace App\Repositories;

use App\Models\MonitorConfig;

class MonitorConfigRepositorySql extends BaseRepositorySql implements MonitorConfigRepositoryInterface
{
    public function __construct(MonitorConfig $model)
    {
        $this->model = $model;
    }
}
