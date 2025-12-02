<?php

namespace App\Repositories;

use App\Models\EventUserGroup;

class EventUserGroupRepositorySql extends BaseRepositorySql implements EventUserGroupRepositoryInterface
{
    public function __construct(EventUserGroup $model)
    {
        $this->model = $model;
    }
}
