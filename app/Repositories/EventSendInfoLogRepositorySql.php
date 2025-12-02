<?php

namespace App\Repositories;

use App\Models\EventSendInfoLog;

class EventSendInfoLogRepositorySql extends BaseRepositorySql implements EventSendInfoLogRepositoryInterface
{
    public function __construct(EventSendInfoLog $model)
    {
        $this->model = $model;
    }
}
