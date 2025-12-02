<?php

namespace App\Repositories;

use App\Models\Telesale;

class TelesaleRepositorySql extends BaseRepositorySql implements TelesaleRepositoryInterface
{
    public function __construct(Telesale $model)
    {
        $this->model = $model;
    }
}
