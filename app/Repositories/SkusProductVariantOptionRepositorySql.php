<?php

namespace App\Repositories;

use App\Models\SkusProductVariantOption;

class SkusProductVariantOptionRepositorySql extends BaseRepositorySql implements SkusProductVariantOptionRepositoryInterface
{
    public function __construct(SkusProductVariantOption $model)
    {
        $this->model = $model;
    }
}
