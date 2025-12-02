<?php

namespace App\Repositories;

use App\Models\EventRegister;

class EventRegisterRepositorySql extends BaseRepositorySql implements EventRegisterRepositoryInterface
{
    public function __construct(EventRegister $model)
    {
        $this->model = $model;
    }
}
