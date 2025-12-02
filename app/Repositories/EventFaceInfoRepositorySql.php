<?php

namespace App\Repositories;

use App\Models\EventFaceInfo;

class EventFaceInfoRepositorySql extends BaseRepositorySql implements EventFaceInfoRepositoryInterface
{
    public function __construct(EventFaceInfo $model)
    {
        $this->model = $model;
    }
}
