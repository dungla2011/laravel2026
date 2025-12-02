<?php

namespace App\Repositories;

use App\Models\ProductFolder;

class ProductFolderRepositorySql extends BaseRepositorySql implements ProductFolderRepositoryInterface
{
    public function __construct(ProductFolder $model)
    {
        $this->model = $model;
    }
}
