<?php

namespace App\Repositories;

use App\Models\EventSendAction;

class EventSendActionRepositorySql extends BaseRepositorySql implements EventSendActionRepositoryInterface
{
    public function __construct(EventSendAction $model)
    {
        $this->model = $model;
    }
}
