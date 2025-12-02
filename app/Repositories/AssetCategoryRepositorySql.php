<?php

namespace App\Repositories;

use App\Models\AssetCategory;

class AssetCategoryRepositorySql extends BaseRepositorySql implements AssetCategoryRepositoryInterface
{
    public function __construct(AssetCategory $model)
    {
        $this->model = $model;
    }
}
