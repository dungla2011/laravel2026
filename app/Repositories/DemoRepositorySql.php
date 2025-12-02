<?php

namespace App\Repositories;

use App\Models\DemoTbl;

class DemoRepositorySql extends BaseRepositorySql implements DemoRepositoryInterface
{
    public function __construct(DemoTbl $model)
    {
        $this->model = $model;
    }
}
