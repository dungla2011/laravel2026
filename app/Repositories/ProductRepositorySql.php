<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepositorySql extends BaseRepositorySql implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }
}
