<?php

namespace App\Repositories;

use App\Models\VpsInstance;

class VpsInstanceRepositorySql extends BaseRepositorySql implements VpsInstanceRepositoryInterface
{
    public function __construct(VpsInstance $model)
    {
        $this->model = $model;
    }
}
