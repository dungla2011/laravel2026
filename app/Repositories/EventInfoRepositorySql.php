<?php

namespace App\Repositories;

use App\Models\EventInfo;

class EventInfoRepositorySql extends BaseRepositorySql implements EventInfoRepositoryInterface
{
    public function __construct(EventInfo $model)
    {
        $this->model = $model;
    }
}
