<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantRepositorySql extends BaseRepositorySql implements ProductVariantRepositoryInterface
{
    public function __construct(ProductVariant $model)
    {
        $this->model = $model;
    }
}
