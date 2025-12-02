<?php

namespace App\Repositories;

use App\Models\SiteMng;

class SiteMngRepositorySql extends BaseRepositorySql implements SiteMngRepositoryInterface
{
    public function __construct(SiteMng $model)
    {
        $this->model = $model;
    }
}
