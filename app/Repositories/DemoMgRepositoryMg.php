<?php

namespace App\Repositories;

use App\Models\demoMg;

class DemoMgRepositoryMg extends BaseRepositoryMg implements DemoMgRepositoryInterface
{
    public function __construct(demoMg $model)
    {
        $this->model = $model;
    }
}
