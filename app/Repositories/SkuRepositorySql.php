<?php

namespace App\Repositories;

use App\Models\Sku;

class SkuRepositorySql extends BaseRepositorySql implements SkuRepositoryInterface
{
    public function __construct(Sku $model)
    {
        $this->model = $model;
    }
}
