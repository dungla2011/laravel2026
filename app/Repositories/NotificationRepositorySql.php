<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepositorySql extends BaseRepositorySql implements NotificationRepositoryInterface
{
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }
}
