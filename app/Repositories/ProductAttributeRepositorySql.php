<?php

namespace App\Repositories;

use App\Models\ProductAttribute;

class ProductAttributeRepositorySql extends BaseRepositorySql implements ProductAttributeRepositoryInterface
{
    public function __construct(ProductAttribute $model)
    {
        $this->model = $model;
    }
}
