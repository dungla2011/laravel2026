<?php

namespace App\Repositories;

use App\Models\EventSetting;

class EventSettingRepositorySql extends BaseRepositorySql implements EventSettingRepositoryInterface
{
    public function __construct(EventSetting $model)
    {
        $this->model = $model;
    }
}
