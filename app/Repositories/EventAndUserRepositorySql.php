<?php

namespace App\Repositories;

use App\Models\EventAndUser;

class EventAndUserRepositorySql extends BaseRepositorySql implements EventAndUserRepositoryInterface
{
    public function __construct(EventAndUser $model)
    {
        $this->model = $model;
    }
}
