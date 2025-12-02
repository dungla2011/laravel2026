<?php

namespace App\Repositories;

use App\Models\FaceData;

class FaceDataRepositorySql extends BaseRepositorySql implements FaceDataRepositoryInterface
{
    public function __construct(FaceData $model)
    {
        $this->model = $model;
    }
}
