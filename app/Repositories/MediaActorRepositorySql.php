<?php

namespace App\Repositories;

use App\Models\MediaActor;

class MediaActorRepositorySql extends BaseRepositorySql implements MediaActorRepositoryInterface
{
    public function __construct(MediaActor $model)
    {
        $this->model = $model;
    }
}
