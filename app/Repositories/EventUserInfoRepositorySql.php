<?php

namespace App\Repositories;

use App\Models\EventUserInfo;

class EventUserInfoRepositorySql extends BaseRepositorySql implements EventUserInfoRepositoryInterface
{
    public function __construct(EventUserInfo $model)
    {
        $this->model = $model;
    }
}
