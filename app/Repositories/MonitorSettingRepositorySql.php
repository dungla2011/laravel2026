<?php

namespace App\Repositories;

use App\Models\MonitorSetting;

class MonitorSettingRepositorySql extends BaseRepositorySql implements MonitorSettingRepositoryInterface
{
    public function __construct(MonitorSetting $model)
    {
        $this->model = $model;
    }
}
