<?php

namespace App\Repositories;

use App\Models\GiaPha;

class TreeMngRepositorySql extends BaseRepositorySql implements TreeMngRepositoryInterface
{
    public function __construct(GiaPha $model)
    {
        $this->model = $model;
    }
}
