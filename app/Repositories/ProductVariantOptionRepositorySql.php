<?php

namespace App\Repositories;

use App\Models\ProductVariantOption;

class ProductVariantOptionRepositorySql extends BaseRepositorySql implements ProductVariantOptionRepositoryInterface
{
    public function __construct(ProductVariantOption $model)
    {
        $this->model = $model;
    }
}
