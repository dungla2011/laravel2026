<?php

namespace App\Repositories;

use App\Models\Asset;

class AssetsRepositorySql extends BaseRepositorySql implements AssetsRepositoryInterface
{
    public function __construct(Asset $model)
    {
        $this->model = $model;
    }
}
