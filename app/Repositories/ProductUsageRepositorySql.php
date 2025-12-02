<?php

namespace App\Repositories;

use App\Models\ProductUsage;

class ProductUsageRepositorySql extends BaseRepositorySql implements ProductUsageRepositoryInterface
{
    public function __construct(ProductUsage $model)
    {
        $this->model = $model;
    }
}
