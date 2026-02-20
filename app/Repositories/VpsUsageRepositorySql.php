<?php

namespace App\Repositories;

use App\Models\VpsUsage;

class VpsUsageRepositorySql extends BaseRepositorySql implements VpsUsageRepositoryInterface
{
    public function __construct(VpsUsage $model)
    {
        $this->model = $model;
    }
}
