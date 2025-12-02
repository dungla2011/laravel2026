<?php

namespace App\Repositories;

use App\Models\UploaderInfo;

class UploaderInfoRepositorySql extends BaseRepositorySql implements UploaderInfoRepositoryInterface
{
    public function __construct(UploaderInfo $model)
    {
        $this->model = $model;
    }
}
